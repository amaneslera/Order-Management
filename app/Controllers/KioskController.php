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
        $data['menu_items'] = $this->menuModel->getAvailableItems();
        $data['categories'] = $this->menuModel->getCategories();
        
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
        $data['cart_items'] = $cart;
        $data['total'] = $this->calculateCartTotal($cart);
        
        return view('kiosk/cart', $data);
    }

    // Add to cart
    public function addToCart()
    {
        $itemId = $this->request->getPost('item_id');
        $quantity = $this->request->getPost('quantity') ?? 1;
        $addons = $this->request->getPost('addons') ?? '';
        $notes = $this->request->getPost('notes') ?? '';

        $menuItem = $this->menuModel->find($itemId);

        if (!$menuItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
        }

        // Check stock availability
        if (!$this->menuModel->hasSufficientStock($itemId, $quantity)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Sorry, insufficient stock available for this item'
            ]);
        }

        $cart = session()->get('cart') ?? [];
        
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
            'cart_count' => count($cart)
        ]);
    }

    // Update cart item
    public function updateCart()
    {
        $cartKey = $this->request->getPost('cart_key');
        $quantity = $this->request->getPost('quantity');

        $cart = session()->get('cart') ?? [];

        if (isset($cart[$cartKey])) {
            if ($quantity > 0) {
                $cart[$cartKey]['quantity'] = $quantity;
            } else {
                unset($cart[$cartKey]);
            }
            session()->set('cart', $cart);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
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

        // Verify stock availability for all items
        foreach ($cart as $item) {
            if (!$this->menuModel->hasSufficientStock($item['id'], $item['quantity'])) {
                $menuItem = $this->menuModel->find($item['id']);
                return redirect()->back()->with('error', 
                    "Insufficient stock for {$menuItem['name']}. Please update your cart.");
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

    // Clear cart
    public function clearCart()
    {
        session()->remove('cart');
        return redirect()->to(base_url('kiosk'))->with('success', 'Cart cleared');
    }
}
