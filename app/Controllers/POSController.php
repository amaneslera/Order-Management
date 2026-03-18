<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;
use App\Models\MenuItemModel;
use App\Models\ActivityLogModel;
use App\Models\InventoryLogModel;

class POSController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $paymentModel;
    protected $menuModel;
    protected $activityLog;
    protected $inventoryLog;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel = new PaymentModel();
        $this->menuModel = new MenuItemModel();
        $this->activityLog = new ActivityLogModel();
        $this->inventoryLog = new InventoryLogModel();
    }

    // Check if user is logged in as cashier
    private function checkAuth()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['cashier', 'admin', 'Admin'])) {
            return redirect()->to('/login');
        }
        return null;
    }

    // POS Dashboard
    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $data['pending_orders'] = $this->orderModel->getOrdersByStatus('pending');
        $data['today_orders'] = $this->orderModel->getTodayOrders();
        
        return view('pos/dashboard', $data);
    }

    // Create a new counter order (for walk-in customers) and open it in POS order details.
    public function createCounterOrder()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $orderNumber = $this->orderModel->generateOrderNumber();
        $orderId = $this->orderModel->insert([
            'order_number' => $orderNumber,
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        if (!$orderId) {
            return redirect()->to(base_url('pos'))->with('error', 'Failed to create a new counter order');
        }

        $this->activityLog->logActivity(
            session()->get('user_id'),
            'create_counter_order',
            "Created counter order #{$orderNumber}"
        );

        return redirect()->to(base_url('pos/order/' . $orderId));
    }

    // Search order by order number
    public function searchOrder()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $orderNumber = $this->request->getGet('order_number');

        if ($orderNumber) {
            $order = $this->orderModel->getOrderByNumber($orderNumber);
            
            if ($order) {
                return redirect()->to(base_url('pos/order/' . $order['id']));
            }
            
            return redirect()->back()->with('error', 'Order not found: ' . $orderNumber);
        }

        return view('pos/search');
    }

    // View order details
    public function viewOrder($orderId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order) {
            return redirect()->to(base_url('pos'))->with('error', 'Order not found');
        }

        $data['order'] = $order;
        $data['menu_items'] = $this->menuModel->getAvailableItems();
        $data['payment'] = $this->paymentModel->getPaymentByOrder($orderId);
        
        return view('pos/order_details', $data);
    }

    // View payment form
    public function viewPayment($orderId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order) {
            return redirect()->to(base_url('pos'))->with('error', 'Order not found');
        }

        $data['order'] = $order;
        $data['payment'] = $this->paymentModel->getPaymentByOrder($orderId);
        
        return view('pos/payment', $data);
    }

    // Update order status
    public function updateOrderStatus()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $orderId = $this->request->getPost('order_id');
        $status = $this->request->getPost('status');

        $updated = $this->orderModel->update($orderId, ['status' => $status]);

        if ($updated) {
            $this->activityLog->logActivity(
                session()->get('user_id'),
                'update_order_status',
                "Order #$orderId status changed to $status"
            );
            
            return $this->response->setJSON(['success' => true, 'message' => 'Order status updated']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update order']);
    }

    // Add item to existing order
    public function addOrderItem()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $orderId = $this->request->getPost('order_id');
        $menuItemId = $this->request->getPost('menu_item_id');
        $quantity = $this->request->getPost('quantity');
        $addons = $this->request->getPost('addons') ?? '';
        $notes = $this->request->getPost('notes') ?? '';

        $menuItem = $this->menuModel->find($menuItemId);

        if (!$menuItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Menu item not found']);
        }

        $existingQty = (int) ($this->orderItemModel
            ->selectSum('quantity')
            ->where('order_id', $orderId)
            ->where('menu_item_id', $menuItemId)
            ->first()['quantity'] ?? 0);

        $intendedQty = $existingQty + (int) $quantity;
        if ((int) $menuItem['stock_quantity'] < $intendedQty) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Only {$menuItem['stock_quantity']} stock available for {$menuItem['name']}"
            ]);
        }

        $orderItemData = [
            'order_id'     => $orderId,
            'menu_item_id' => $menuItemId,
            'quantity'     => $quantity,
            'price'        => $menuItem['price'],
            'addons'       => $addons,
            'notes'        => $notes,
        ];

        $inserted = $this->orderItemModel->insert($orderItemData);

        if ($inserted) {
            // Update order total
            $this->updateOrderTotal($orderId);

            $this->activityLog->logActivity(
                session()->get('user_id'),
                'add_order_item',
                "Added item to order #$orderId"
            );

            return $this->response->setJSON(['success' => true, 'message' => 'Item added to order']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to add item']);
    }

    // Remove order item
    public function removeOrderItem()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $itemId = $this->request->getPost('item_id');
        $orderItem = $this->orderItemModel->find($itemId);

        if ($orderItem) {
            $orderId = $orderItem['order_id'];
            $this->orderItemModel->delete($itemId);
            $this->updateOrderTotal($orderId);

            $this->activityLog->logActivity(
                session()->get('user_id'),
                'remove_order_item',
                "Removed item from order #$orderId"
            );

            return $this->response->setJSON(['success' => true, 'message' => 'Item removed']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
    }

    // Update quantity of an order item
    public function updateOrderItemQuantity()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $itemId = (int) $this->request->getPost('item_id');
        $quantity = (int) $this->request->getPost('quantity');

        if ($quantity < 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Quantity must be at least 1']);
        }

        $orderItem = $this->orderItemModel->find($itemId);
        if (!$orderItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Order item not found']);
        }

        $order = $this->orderModel->find($orderItem['order_id']);
        if (!$order || $order['status'] !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only pending orders can be edited']);
        }

        $menuItem = $this->menuModel->find($orderItem['menu_item_id']);
        if (!$menuItem) {
            return $this->response->setJSON(['success' => false, 'message' => 'Menu item not found']);
        }

        $otherQtyRow = $this->orderItemModel
            ->selectSum('quantity')
            ->where('order_id', $orderItem['order_id'])
            ->where('menu_item_id', $orderItem['menu_item_id'])
            ->where('id !=', $itemId)
            ->first();
        $otherQty = (int) ($otherQtyRow['quantity'] ?? 0);

        if ((int) $menuItem['stock_quantity'] < ($otherQty + $quantity)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Only {$menuItem['stock_quantity']} stock available for {$menuItem['name']}"
            ]);
        }

        $updated = $this->orderItemModel->update($itemId, ['quantity' => $quantity]);
        if (!$updated) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update quantity']);
        }

        $this->updateOrderTotal($orderItem['order_id']);

        $this->activityLog->logActivity(
            session()->get('user_id'),
            'update_order_item_quantity',
            "Updated quantity for item #{$itemId} in order #{$orderItem['order_id']}"
        );

        return $this->response->setJSON(['success' => true, 'message' => 'Quantity updated']);
    }

    // Process payment
    public function processPayment()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $orderId = $this->request->getPost('order_id');
        $paymentMethod = $this->request->getPost('payment_method');
        $amount = $this->request->getPost('amount');

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Order not found']);
        }

        if (($order['status'] ?? null) !== 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only pending orders can be paid']);
        }

        $amount = (float) $amount;
        $orderTotal = (float) ($order['total_amount'] ?? 0);
        if ($amount < $orderTotal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Amount received is less than order total']);
        }

        // Get order items to deduct stock
        $orderItems = $this->orderItemModel->where('order_id', $orderId)->findAll();

        // Verify stock availability before processing payment
        foreach ($orderItems as $item) {
            if (!$this->menuModel->hasSufficientStock($item['menu_item_id'], $item['quantity'])) {
                $menuItem = $this->menuModel->find($item['menu_item_id']);
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => "Insufficient stock for {$menuItem['name']}. Current stock: {$menuItem['stock_quantity']}"
                ]);
            }
        }

        // Create payment record
        $paymentData = [
            'order_id'       => $orderId,
            'payment_method' => $paymentMethod,
            'amount'         => $amount,
            'payment_date'   => date('Y-m-d H:i:s'),
        ];

        $paymentId = $this->paymentModel->insert($paymentData);

        if ($paymentId) {
            // Deduct stock for each item in the order
            foreach ($orderItems as $item) {
                $menuItem = $this->menuModel->find($item['menu_item_id']);
                $previousStock = $menuItem['stock_quantity'];
                
                // Deduct stock
                $this->menuModel->deductStock($item['menu_item_id'], $item['quantity']);
                
                // Get new stock
                $updatedItem = $this->menuModel->find($item['menu_item_id']);
                $newStock = $updatedItem['stock_quantity'];
                
                // Log inventory change
                $this->inventoryLog->logInventoryChange(
                    $item['menu_item_id'],
                    'deduct',
                    -$item['quantity'],
                    $previousStock,
                    $newStock,
                    session()->get('user_id'),
                    $orderId,
                    "Stock deducted for order #{$order['order_number']}"
                );
            }

            // Update order status to paid
            $this->orderModel->update($orderId, ['status' => 'paid']);

            $this->activityLog->logActivity(
                session()->get('user_id'),
                'process_payment',
                "Payment processed for order #$orderId via $paymentMethod. Stock deducted for all items."
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment processed successfully and inventory updated',
                'payment_id' => $paymentId
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to process payment']);
    }

    // View receipt
    public function viewReceipt($orderId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $order = $this->orderModel->getOrderWithItems($orderId);
        $payment = $this->paymentModel->getPaymentByOrder($orderId);

        if (!$order) {
            return redirect()->to(base_url('pos'))->with('error', 'Order not found');
        }

        $data['order'] = $order;
        $data['payment'] = $payment;
        
        return view('pos/receipt', $data);
    }

    // List all orders
    public function listOrders()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $status = $this->request->getGet('status');
        
        if ($status) {
            $data['orders'] = $this->orderModel->getOrdersByStatus($status);
        } else {
            $data['orders'] = $this->orderModel->orderBy('created_at', 'DESC')->findAll();
        }

        $data['selected_status'] = $status;
        
        return view('pos/orders_list', $data);
    }

    // Update order total
    private function updateOrderTotal($orderId)
    {
        $total = $this->orderItemModel->getOrderTotal($orderId);
        $this->orderModel->update($orderId, ['total_amount' => $total]);
    }
}
