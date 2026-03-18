<?php

namespace App\Controllers;

use App\Models\MenuItemModel;
use App\Models\ActivityLogModel;
use App\Models\InventoryLogModel;

class MenuController extends BaseController
{
    protected $menuModel;
    protected $activityLog;
    protected $inventoryLog;

    public function __construct()
    {
        $this->menuModel = new MenuItemModel();
        $this->activityLog = new ActivityLogModel();
        $this->inventoryLog = new InventoryLogModel();
    }

    // Check if user is admin
    private function checkAuth()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['admin', 'Admin'])) {
            return redirect()->to(base_url('login'));
        }
        return null;
    }

    // List all menu items
    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $data['menu_items'] = $this->menuModel->findAll();
        $data['categories'] = $this->menuModel->getCategories();

        return view('admin/menu/list', $data);
    }

    // Add menu item
    public function add()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();

            $rules = [
                'name'        => 'required|min_length[3]|max_length[255]',
                'category'    => 'required',
                'price'       => 'required|decimal',
                'status'      => 'required|in_list[available,unavailable]',
                'image'       => 'uploaded[image]|max_size[image,2048]|is_image[image]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $imageFile = $this->request->getFile('image');
            $imageName = null;

            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $imageName = $imageFile->getRandomName();
                $imageFile->move(ROOTPATH . 'public/uploads/menu', $imageName);
            }

            $menuData = [
                'name'        => $this->request->getPost('name'),
                'category'    => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'price'       => $this->request->getPost('price'),
                'image'       => $imageName,
                'status'      => $this->request->getPost('status'),
            ];

            if ($this->menuModel->insert($menuData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'add_menu_item',
                    "Added menu item: {$menuData['name']}"
                );

                return redirect()->to(base_url('admin/menu'))->with('success', 'Menu item added successfully');
            }

            return redirect()->back()->with('error', 'Failed to add menu item');
        }

        return view('admin/menu/add');
    }

    // Edit menu item
    public function edit($itemId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $menuItem = $this->menuModel->find($itemId);

        if (!$menuItem) {
            return redirect()->to(base_url('admin/menu'))->with('error', 'Menu item not found');
        }

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();

            $rules = [
                'name'        => 'required|min_length[3]|max_length[255]',
                'category'    => 'required',
                'price'       => 'required|decimal',
                'status'      => 'required|in_list[available,unavailable]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $menuData = [
                'name'        => $this->request->getPost('name'),
                'category'    => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'price'       => $this->request->getPost('price'),
                'status'      => $this->request->getPost('status'),
            ];

            // Handle image upload
            $imageFile = $this->request->getFile('image');
            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                // Delete old image
                if ($menuItem['image']) {
                    $oldImagePath = ROOTPATH . 'public/uploads/menu/' . $menuItem['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $imageName = $imageFile->getRandomName();
                $imageFile->move(ROOTPATH . 'public/uploads/menu', $imageName);
                $menuData['image'] = $imageName;
            }

            if ($this->menuModel->update($itemId, $menuData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'edit_menu_item',
                    "Updated menu item: {$menuData['name']}"
                );

                return redirect()->to(base_url('admin/menu'))->with('success', 'Menu item updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update menu item');
        }

        $data['menu_item'] = $menuItem;
        return view('admin/menu/edit', $data);
    }

    // Delete menu item
    public function delete($itemId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $menuItem = $this->menuModel->find($itemId);

        if (!$menuItem) {
            return redirect()->to(base_url('admin/menu'))->with('error', 'Menu item not found');
        }

        // Delete image file
        if ($menuItem['image']) {
            $imagePath = ROOTPATH . 'public/uploads/menu/' . $menuItem['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->menuModel->delete($itemId)) {
            $this->activityLog->logActivity(
                session()->get('user_id'),
                'delete_menu_item',
                "Deleted menu item: {$menuItem['name']}"
            );

            return redirect()->to(base_url('admin/menu'))->with('success', 'Menu item deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete menu item');
    }

    // Toggle status
    public function toggleStatus($itemId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $menuItem = $this->menuModel->find($itemId);

        if ($menuItem) {
            $newStatus = $menuItem['status'] === 'available' ? 'unavailable' : 'available';
            $this->menuModel->update($itemId, ['status' => $newStatus]);

            return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    // Adjust stock quantity
    public function adjustStock()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        if (strtoupper($this->request->getMethod()) === 'POST') {
            try {
                $json = $this->request->getJSON(true);
                $itemId = $json['item_id'] ?? $this->request->getPost('item_id');
                $action = $json['action'] ?? $this->request->getPost('action'); // add or subtract
                $quantity = (int) ($json['quantity'] ?? $this->request->getPost('quantity'));
                $reason = $json['reason'] ?? $this->request->getPost('reason') ?? 'Manual Stock Adjustment';

                if (empty($itemId) || !in_array($action, ['add', 'subtract'], true) || $quantity <= 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid stock adjustment payload'
                    ]);
                }

                $menuItem = $this->menuModel->find((int) $itemId);

                if (!$menuItem) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
                }

                $currentStock = (int) $menuItem['stock_quantity'];
                $newStock = ($action === 'add') ? $currentStock + $quantity : $currentStock - $quantity;

                // Prevent negative stock
                if ($newStock < 0) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Stock cannot be negative']);
                }

                if ($this->menuModel->update((int) $itemId, ['stock_quantity' => $newStock])) {
                    $logAction = $action === 'add' ? 'add' : 'deduct';
                    $quantityChange = $action === 'add' ? $quantity : -$quantity;

                    $this->inventoryLog->logInventoryChange(
                        (int) $itemId,
                        $logAction,
                        $quantityChange,
                        $currentStock,
                        $newStock,
                        (int) session()->get('user_id'),
                        null,
                        $reason
                    );

                    $this->activityLog->logActivity(
                        session()->get('user_id'),
                        'adjust_stock',
                        "Adjusted stock for {$menuItem['name']}: {$currentStock} → {$newStock} ({$reason})"
                    );

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Stock adjusted successfully',
                        'new_stock' => $newStock
                    ]);
                }

                return $this->response->setJSON(['success' => false, 'message' => 'Failed to adjust stock']);
            } catch (\Throwable $e) {
                log_message('error', 'adjustStock failed: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Server error while adjusting stock'
                ]);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    }

    // Set low stock threshold
    public function setLowStockThreshold()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        if (strtoupper($this->request->getMethod()) === 'POST') {
            try {
                $json = $this->request->getJSON(true);
                $itemId = $json['item_id'] ?? $this->request->getPost('item_id');
                $threshold = (int) ($json['threshold'] ?? $this->request->getPost('threshold'));

                if (empty($itemId)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Item ID is required']);
                }

                $menuItem = $this->menuModel->find((int) $itemId);

                if (!$menuItem) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
                }

                if ($threshold < 0) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Threshold cannot be negative']);
                }

                if ($this->menuModel->update((int) $itemId, ['low_stock_threshold' => $threshold])) {
                    $this->activityLog->logActivity(
                        session()->get('user_id'),
                        'set_low_stock_threshold',
                        "Set low stock threshold for {$menuItem['name']} to {$threshold}"
                    );

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Low stock threshold updated successfully',
                        'threshold' => $threshold
                    ]);
                }

                return $this->response->setJSON(['success' => false, 'message' => 'Failed to set threshold']);
            } catch (\Throwable $e) {
                log_message('error', 'setLowStockThreshold failed: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Server error while updating threshold'
                ]);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    }

    // Get inventory summary
    public function inventorySummary()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $items = $this->menuModel->findAll();
        
        $totalItems = count($items);
        $lowStockItems = 0;
        $outOfStockItems = 0;
        $totalValue = 0;

        foreach ($items as $item) {
            $stock = (int) ($item['stock_quantity'] ?? 0);
            $threshold = (int) ($item['low_stock_threshold'] ?? 0);

            if ($stock === 0) {
                $outOfStockItems++;
            } elseif ($stock <= $threshold) {
                $lowStockItems++;
            }
            $totalValue += ($stock * (float) $item['price']);
        }

        $data['total_items'] = $totalItems;
        $data['low_stock_items'] = $lowStockItems;
        $data['out_of_stock_items'] = $outOfStockItems;
        $data['total_value'] = $totalValue;
        $data['menu_items'] = $items;
        $data['recent_logs'] = $this->inventoryLog->getLogsWithDetails(20);
        $data['categories'] = $this->menuModel->getCategories();

        return view('admin/menu/inventory', $data);
    }

    // Check stock levels and create alerts
    public function checkStockLevels()
    {
        $stockAlertModel = new \App\Models\StockAlertModel();
        $items = $this->menuModel->findAll();
        $alertsCreated = 0;

        foreach ($items as $item) {
            $stock = (int) ($item['stock_quantity'] ?? 0);
            $threshold = (int) ($item['low_stock_threshold'] ?? 0);

            // Check for out of stock
            if ($stock === 0) {
                if ($stockAlertModel->createAlert($item['id'], 'out_of_stock', $stock, 0)) {
                    $alertsCreated++;
                    // Trigger SMS notification
                    $this->sendStockAlertSMS($item, 'out_of_stock');
                }
            }
            // Check for low stock
            elseif ($stock > 0 && $stock <= $threshold) {
                if ($stockAlertModel->createAlert($item['id'], 'low_stock', $stock, $threshold)) {
                    $alertsCreated++;
                    // Trigger SMS notification
                    $this->sendStockAlertSMS($item, 'low_stock');
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'alerts_created' => $alertsCreated,
            'message' => "Stock levels checked. $alertsCreated alerts created."
        ]);
    }

    // Send SMS alert for stock level
    private function sendStockAlertSMS($item, $type)
    {
        // Get Twilio configuration
        $twilioSid = getenv('TWILIO_ACCOUNT_SID');
        $twilioToken = getenv('TWILIO_AUTH_TOKEN');
        $twilioPhone = getenv('TWILIO_PHONE_NUMBER');
        $adminPhone = getenv('ADMIN_PHONE_NUMBER');

        if (!$twilioSid || !$twilioToken || !$adminPhone) {
            return false; // SMS service not configured
        }

        $message = $type === 'out_of_stock'
            ? "ALERT: {$item['name']} is OUT OF STOCK."
            : "ALERT: {$item['name']} stock is LOW ({$item['stock_quantity']} remaining).";

        // Use cURL to connect to Twilio API
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$twilioSid}:{$twilioToken}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'From' => $twilioPhone,
            'To'   => $adminPhone,
            'Body' => $message
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response !== false;
    }

    // Display stock alerts dashboard
    public function alerts()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;
        $lowStockItems = $this->menuModel->getLowStockItems();
        $outOfStockItems = $this->menuModel->getOutOfStockItems();

        $activeAlerts = $this->buildRealtimeActiveAlerts($lowStockItems, $outOfStockItems);
        $pendingSms = count($activeAlerts);

        $data['active_alerts'] = $activeAlerts;
        $data['alert_stats'] = [
            'total_today' => count($lowStockItems) + count($outOfStockItems),
            'low_stock' => count($lowStockItems),
            'out_of_stock' => count($outOfStockItems),
            'pending_sms' => $pendingSms,
        ];
        $data['low_stock_items'] = $lowStockItems;
        $data['out_of_stock_items'] = $outOfStockItems;

        return view('admin/menu/alerts', $data);
    }

    // Acknowledge/dismiss alert
    public function dismissAlert()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $alertId = $this->request->getPost('alert_id');
        if (!$alertId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Alert ID required']);
        }

        $stockAlertModel = new \App\Models\StockAlertModel();
        $deleted = $stockAlertModel->delete($alertId);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Alert dismissed' : 'Failed to dismiss alert'
        ]);
    }

    // Get alerts via API
    public function getAlerts()
    {
        $lowStockItems = $this->menuModel->getLowStockItems();
        $outOfStockItems = $this->menuModel->getOutOfStockItems();

        $activeAlerts = $this->buildRealtimeActiveAlerts($lowStockItems, $outOfStockItems);
        $pendingSms = count($activeAlerts);

        $alerts = [
            'active' => $activeAlerts,
            'stats' => [
                'total_today' => count($lowStockItems) + count($outOfStockItems),
                'low_stock' => count($lowStockItems),
                'out_of_stock' => count($outOfStockItems),
                'pending_sms' => $pendingSms,
            ]
        ];
        return $this->response->setJSON($alerts);
    }

    // Build active alerts directly from current inventory state.
    private function buildRealtimeActiveAlerts(array $lowStockItems, array $outOfStockItems): array
    {
        $alerts = [];

        foreach ($outOfStockItems as $item) {
            $alerts[] = [
                'id' => null,
                'menu_item_id' => (int) $item['id'],
                'name' => $item['name'],
                'category' => $item['category'],
                'alert_type' => 'out_of_stock',
                'current_stock' => (int) $item['stock_quantity'],
                'threshold' => (int) $item['low_stock_threshold'],
                'sent_sms' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'is_realtime' => true,
            ];
        }

        foreach ($lowStockItems as $item) {
            $alerts[] = [
                'id' => null,
                'menu_item_id' => (int) $item['id'],
                'name' => $item['name'],
                'category' => $item['category'],
                'alert_type' => 'low_stock',
                'current_stock' => (int) $item['stock_quantity'],
                'threshold' => (int) $item['low_stock_threshold'],
                'sent_sms' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'is_realtime' => true,
            ];
        }

        usort($alerts, static function ($a, $b) {
            return $a['current_stock'] <=> $b['current_stock'];
        });

        return $alerts;
    }
}
