<?php

namespace App\Controllers;

use App\Models\MenuItemModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class KioskController extends BaseController
{
    protected $menuModel;
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->menuModel = new MenuItemModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    // Display kiosk menu
    public function index()
    {
        $menuItems = $this->menuModel->getAvailableItems();
        $cart = session()->get('cart') ?? [];
        $cartQuantities = $this->getCartQuantitiesByItem($cart);
        $itemIds = array_map(static fn($item) => (int) ($item['id'] ?? 0), $menuItems);
        $pendingReserved = $this->getPendingReservedQuantities($itemIds);

        foreach ($menuItems as &$item) {
            $itemId = (int) ($item['id'] ?? 0);
            $dbStock = (int) ($item['stock_quantity'] ?? 0);
            $reservedQty = (int) ($pendingReserved[$itemId] ?? 0);
            $reservableStock = max(0, $dbStock - $reservedQty);
            $inCartQty = (int) ($cartQuantities[$itemId] ?? 0);

            $item['available_stock'] = $reservableStock;
            $item['remaining_stock'] = max(0, $reservableStock - $inCartQty);
        }
        unset($item);

        $data['menu_items'] = $menuItems;
        $data['categories'] = $this->menuModel->getCategories();
        $data['cart_count'] = $this->getCartItemCount($cart);
        
        return view('kiosk/menu', $data);
    }

    // Get menu items by category (AJAX)
    public function getMenuByCategory($category)
    {
        $items = $this->menuModel->getItemsByCategory($category);
        return $this->response->setJSON($items);
    }

    // View cart
    public function cart()
    {
        $cart = session()->get('cart') ?? [];
        $cartQuantities = $this->getCartQuantitiesByItem($cart);
        $itemIds = array_keys($cartQuantities);
        $pendingReserved = $this->getPendingReservedQuantities($itemIds);

        foreach ($cart as $key => $item) {
            $itemId = (int) ($item['id'] ?? 0);
            $menuItem = $this->menuModel->find($itemId);
            $dbStock = (int) ($menuItem['stock_quantity'] ?? 0);
            $reservedQty = (int) ($pendingReserved[$itemId] ?? 0);
            $reservableStock = max(0, $dbStock - $reservedQty);

            $lineQty = (int) ($item['quantity'] ?? 0);
            $otherLinesQty = max(0, (int) ($cartQuantities[$itemId] ?? 0) - $lineQty);
            $lineMaxQty = max(0, $reservableStock - $otherLinesQty);

            $cart[$key]['available_stock'] = $reservableStock;
            $cart[$key]['line_max_quantity'] = $lineMaxQty;
        }

        $data['cart_items'] = $cart;
        $data['total'] = $this->calculateCartTotal($cart);
        
        return view('kiosk/cart', $data);
    }

    // Add to cart
    public function addToCart()
    {
        $itemId = (int) $this->request->getPost('item_id');
        $quantity = max(1, (int) ($this->request->getPost('quantity') ?? 1));
        $addons = $this->request->getPost('addons') ?? '';
        $notes = $this->request->getPost('notes') ?? '';

        $menuItem = $this->menuModel->find($itemId);

        if (!$menuItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
        }

        $cart = session()->get('cart') ?? [];
        $existingQuantity = $this->getCartItemQuantityForItem($cart, $itemId);
        $intendedQuantity = $existingQuantity + $quantity;
        $reservableStock = $this->getReservableStock($itemId, $menuItem);

        // Check stock availability against total quantity of this item already in cart.
        if ($reservableStock < $intendedQuantity) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Only ' . $reservableStock . ' item(s) available in stock.',
                'remaining_stock' => max(0, $reservableStock - $existingQuantity),
                'item_id' => $itemId,
            ]);
        }
        
        $cartItemKey = $itemId . '_' . md5($addons);
        
        if (isset($cart[$cartItemKey])) {
            $cart[$cartItemKey]['quantity'] += $quantity;
        } else {
            $cart[$cartItemKey] = [
                'id'       => $itemId,
                'name'     => $menuItem['name'],
                'price'    => $menuItem['price'],
                'quantity' => $quantity,
                'addons'   => $addons,
                'notes'    => $notes,
                'image'    => $menuItem['image'],
            ];
        }

        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $this->getCartItemCount($cart),
            'remaining_stock' => max(0, $reservableStock - $this->getCartItemQuantityForItem($cart, $itemId)),
            'item_id' => $itemId,
        ]);
    }

    // Update cart item
    public function updateCart()
    {
        $cartKey = $this->request->getPost('cart_key');
        $quantity = (int) $this->request->getPost('quantity');

        $cart = session()->get('cart') ?? [];

        if (isset($cart[$cartKey])) {
            if ($quantity > 0) {
                $itemId = (int) ($cart[$cartKey]['id'] ?? 0);
                $menuItem = $this->menuModel->find($itemId);

                if (!$menuItem) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Item no longer available.'
                    ]);
                }

                $otherCartQuantity = $this->getCartItemQuantityForItem($cart, $itemId, $cartKey);
                $intendedQuantity = $otherCartQuantity + $quantity;
                $reservableStock = $this->getReservableStock($itemId, $menuItem);

                if ($reservableStock < $intendedQuantity) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Only ' . $reservableStock . ' item(s) available in stock.'
                    ]);
                }

                $cart[$cartKey]['quantity'] = $quantity;
            } else {
                unset($cart[$cartKey]);
            }
            session()->set('cart', $cart);
            return $this->response->setJSON([
                'success' => true,
                'cart_count' => $this->getCartItemCount($cart)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Cart item not found.'
        ]);
    }

    // Remove from cart
    public function removeFromCart()
    {
        $cartKey = $this->request->getPost('cart_key');
        $cart = session()->get('cart') ?? [];

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->set('cart', $cart);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    // Checkout and create order
    public function checkout()
    {
        $cart = session()->get('cart') ?? [];

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        // Verify stock availability using aggregated quantity per menu item.
        $requiredByItem = [];
        foreach ($cart as $item) {
            $itemId = (int) ($item['id'] ?? 0);
            $requiredByItem[$itemId] = ($requiredByItem[$itemId] ?? 0) + (int) ($item['quantity'] ?? 0);
        }

        foreach ($requiredByItem as $itemId => $requiredQty) {
            $menuItem = $this->menuModel->find($itemId);
            if (!$menuItem || (int) ($menuItem['stock_quantity'] ?? 0) < $requiredQty) {
                $itemName = $menuItem['name'] ?? 'an item';
                return redirect()->back()->with('error', 
                    "Insufficient stock for {$itemName}. Please update your cart.");
            }
        }

        // Generate order number
        $orderNumber = $this->orderModel->generateOrderNumber();

        // Calculate total
        $total = $this->calculateCartTotal($cart);

        // Create order
        $orderData = [
            'order_number' => $orderNumber,
            'status'       => 'pending',
            'total_amount' => $total,
        ];

        $orderId = $this->orderModel->insert($orderData);

        if ($orderId) {
            // Add order items (stock will be reserved but not deducted until payment)
            foreach ($cart as $item) {
                $orderItemData = [
                    'order_id'     => $orderId,
                    'menu_item_id' => $item['id'],
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'],
                    'addons'       => $item['addons'] ?? '',
                    'notes'        => $item['notes'] ?? '',
                ];
                $this->orderItemModel->insert($orderItemData);
            }

            // Clear cart
            session()->remove('cart');

            // Redirect to order confirmation with barcode
            return redirect()->to(base_url('kiosk/order-confirmation/' . $orderId));
        }

        return redirect()->back()->with('error', 'Failed to create order');
    }

    // Order confirmation page
    public function orderConfirmation($orderId)
    {
        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order) {
            return redirect()->to(base_url('kiosk'))->with('error', 'Order not found');
        }

        $data['order'] = $order;
        
        return view('kiosk/order_confirmation', $data);
    }

    // Calculate cart total
    private function calculateCartTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Calculate total quantity of items in cart
    private function getCartItemCount(array $cart): int
    {
        $count = 0;
        foreach ($cart as $item) {
            $count += (int) ($item['quantity'] ?? 0);
        }

        return $count;
    }

    // Count total quantity of a specific menu item across all cart lines.
    private function getCartItemQuantityForItem(array $cart, int $itemId, ?string $excludeKey = null): int
    {
        $quantity = 0;

        foreach ($cart as $key => $item) {
            if ($excludeKey !== null && $key === $excludeKey) {
                continue;
            }

            if ((int) ($item['id'] ?? 0) === $itemId) {
                $quantity += (int) ($item['quantity'] ?? 0);
            }
        }

        return $quantity;
    }

    // Build a map of menu_item_id => total quantity in cart.
    private function getCartQuantitiesByItem(array $cart): array
    {
        $quantities = [];

        foreach ($cart as $item) {
            $itemId = (int) ($item['id'] ?? 0);
            $quantities[$itemId] = ($quantities[$itemId] ?? 0) + (int) ($item['quantity'] ?? 0);
        }

        return $quantities;
    }

    // Get reserved quantities from all pending orders grouped by menu item.
    private function getPendingReservedQuantities(array $itemIds = []): array
    {
        $builder = $this->orderItemModel->builder();
        $builder->select('menu_item_id, SUM(quantity) AS reserved_qty');
        $builder->join('orders', 'orders.id = order_items.order_id');
        $builder->where('orders.status', 'pending');

        if (!empty($itemIds)) {
            $builder->whereIn('menu_item_id', $itemIds);
        }

        $builder->groupBy('menu_item_id');
        $rows = $builder->get()->getResultArray();

        $reserved = [];
        foreach ($rows as $row) {
            $reserved[(int) $row['menu_item_id']] = (int) ($row['reserved_qty'] ?? 0);
        }

        return $reserved;
    }

    // Stock available for new kiosk cart additions.
    private function getReservableStock(int $itemId, ?array $menuItem = null): int
    {
        $menuItem = $menuItem ?? $this->menuModel->find($itemId);
        if (!$menuItem) {
            return 0;
        }

        $dbStock = (int) ($menuItem['stock_quantity'] ?? 0);
        $reservedQty = (int) ($this->getPendingReservedQuantities([$itemId])[$itemId] ?? 0);

        return max(0, $dbStock - $reservedQty);
    }

    // Clear cart
    public function clearCart()
    {
        session()->remove('cart');
        return redirect()->to(base_url('kiosk'))->with('success', 'Cart cleared');
    }
}
