<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Models\SMSLogModel;
use App\Models\MenuItemModel;
use App\Libraries\EmailService;

class AdminController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $paymentModel;
    protected $userModel;
    protected $activityLog;
    protected $emailService;
    protected $smsLogModel;
    protected $menuModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel = new PaymentModel();
        $this->userModel = new UserModel();
        $this->activityLog = new ActivityLogModel();
        $this->emailService = new EmailService();
        $this->smsLogModel = new SMSLogModel();
        $this->menuModel = new MenuItemModel();
    }

    // Check if user is admin
    private function checkAuth()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['admin', 'Admin'])) {
            return redirect()->to('/login');
        }
        return null;
    }

    // Normalize role values from forms/DB to canonical values used by the app.
    private function normalizeRole(string $role, string $fallback = 'cashier'): string
    {
        $normalized = strtolower(trim($role));
        if ($normalized === 'admin') {
            return 'Admin';
        }
        if ($normalized === 'cashier') {
            return 'cashier';
        }

        return $fallback;
    }

    // Admin Dashboard
    public function dashboard()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Get statistics
        $data['total_orders_today'] = count($this->orderModel->getTodayOrders());
        
        // Total revenue today
        $todayOrders = $this->orderModel->where('DATE(created_at)', date('Y-m-d'))
                                        ->where('status !=', 'cancelled')
                                        ->findAll();
        $data['total_revenue_today'] = array_sum(array_column($todayOrders, 'total_amount'));

        // Total pending orders
        $data['pending_orders'] = count($this->orderModel->getOrdersByStatus('pending'));

        // Recent orders
        $data['recent_orders'] = $this->orderModel->orderBy('created_at', 'DESC')->findAll(10);

        // Top selling items (last 30 days)
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        $data['top_selling'] = $this->orderItemModel->getTopSellingItems(5, $startDate, $endDate);

        // Inventory alerts
        $data['low_stock_count'] = count($this->menuModel->getLowStockItems());
        $data['out_of_stock_count'] = count($this->menuModel->getOutOfStockItems());
        $data['low_stock_items'] = $this->menuModel->getLowStockItems();

        return view('admin/dashboard', $data);
    }

    // View sales reports
    public function reports()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $reportType = $this->request->getGet('type') ?? 'daily';
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $export = $this->request->getGet('export');

        // Set default date range
        if (!$startDate || !$endDate) {
            $anchorDate = date('Y-m-d');
            $latestOrder = $this->orderModel->orderBy('created_at', 'DESC')->first();
            if (!empty($latestOrder['created_at'])) {
                $anchorDate = date('Y-m-d', strtotime($latestOrder['created_at']));
            }

            switch ($reportType) {
                case 'daily':
                    $startDate = $anchorDate;
                    $endDate = $anchorDate;
                    break;
                case 'weekly':
                    $startDate = date('Y-m-d', strtotime($anchorDate . ' -6 days'));
                    $endDate = $anchorDate;
                    break;
                case 'monthly':
                    $startDate = date('Y-m-01', strtotime($anchorDate));
                    $endDate = date('Y-m-t', strtotime($anchorDate));
                    break;
            }
        }

        // Get sales report
        $data['sales_report'] = $this->orderModel->getSalesReport($startDate, $endDate);
        
        // Get top selling items
        $data['top_selling'] = $this->orderItemModel->getTopSellingItems(10, $startDate, $endDate);

        // Get payment methods summary
        $data['payment_methods'] = $this->paymentModel->getPaymentMethodsSummary($startDate, $endDate);

        // Calculate totals
        $totalSales = 0;
        $totalOrders = 0;
        foreach ($data['sales_report'] as $row) {
            $totalSales += $row['total_sales'];
            $totalOrders += $row['total_orders'];
        }

        $data['total_sales'] = $totalSales;
        $data['total_orders'] = $totalOrders;
        $data['report_type'] = $reportType;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        // Export functionality
        if ($export === 'csv') {
            return $this->exportReportToCSV($data);
        }

        return view('admin/reports', $data);
    }

    // Export report to CSV
    private function exportReportToCSV($data)
    {
        $filename = 'sales_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['SALES REPORT', $data['report_type'], $data['start_date'] . ' to ' . $data['end_date']]);
        fputcsv($output, []);
        fputcsv($output, ['SUMMARY']);
        fputcsv($output, ['Total Revenue', '₱' . number_format($data['total_sales'], 2)]);
        fputcsv($output, ['Total Orders', $data['total_orders']]);
        fputcsv($output, ['Average Order Value', '₱' . number_format($data['total_orders'] > 0 ? $data['total_sales'] / $data['total_orders'] : 0, 2)]);
        fputcsv($output, []);
        
        // Daily breakdown
        fputcsv($output, ['DAILY BREAKDOWN']);
        fputcsv($output, ['Date', 'Orders', 'Sales', 'Avg Order Value']);
        foreach ($data['sales_report'] as $row) {
            fputcsv($output, [
                date('M d, Y', strtotime($row['date'])),
                $row['total_orders'],
                '₱' . number_format($row['total_sales'], 2),
                '₱' . number_format($row['total_sales'] / $row['total_orders'], 2)
            ]);
        }
        fputcsv($output, []);
        
        // Top selling items
        fputcsv($output, ['TOP SELLING ITEMS']);
        fputcsv($output, ['Rank', 'Item', 'Category', 'Sold', 'Revenue']);
        foreach ($data['top_selling'] as $index => $item) {
            fputcsv($output, [
                $index + 1,
                $item['name'],
                $item['category'],
                $item['total_quantity'],
                '₱' . number_format($item['total_revenue'], 2)
            ]);
        }
        fputcsv($output, []);
        
        // Payment methods
        fputcsv($output, ['PAYMENT METHODS']);
        fputcsv($output, ['Payment Method', 'Count', 'Total']);
        foreach ($data['payment_methods'] as $method) {
            fputcsv($output, [
                ucfirst($method['payment_method']),
                $method['count'],
                '₱' . number_format($method['total'], 2)
            ]);
        }
        
        fclose($output);
        exit;
    }

    // Activity logs
    public function activityLogs()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $action = trim((string) ($this->request->getGet('action') ?? ''));
        $role = trim((string) ($this->request->getGet('role') ?? ''));
        $dateFrom = trim((string) ($this->request->getGet('date_from') ?? ''));
        $dateTo = trim((string) ($this->request->getGet('date_to') ?? ''));

        $filters = [
            'action' => $action,
            'role' => $role,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];

        $data['logs'] = $this->activityLog->getFilteredLogsWithUsers($filters, 100);
        $data['selected_action'] = $action;
        $data['selected_role'] = $role;
        $data['selected_date_from'] = $dateFrom;
        $data['selected_date_to'] = $dateTo;

        return view('admin/activity_logs', $data);
    }

    // User management
    public function users()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $data['users'] = $this->userModel->findAll();

        return view('admin/users', $data);
    }

    // Add user
    public function addUser()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $username = trim((string) $this->request->getPost('username'));
            $password = (string) $this->request->getPost('password');
            $role = (string) $this->request->getPost('role');

            if ($username === '' || $password === '') {
                return redirect()->to('/admin/users')->with('error', 'Username and password are required');
            }

            $userData = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => $this->normalizeRole($role),
            ];

            if ($this->userModel->insert($userData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'add_user',
                    "Added new user: {$userData['username']}"
                );
                
                return redirect()->to('/admin/users')->with('success', 'User added successfully');
            }

            return redirect()->to('/admin/users')->with('error', 'Failed to add user');
        }

        return redirect()->to('/admin/users');
    }

    // Edit user
    public function editUser($userId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $username = trim((string) $this->request->getPost('username'));
            $role = (string) $this->request->getPost('role');

            if ($username === '') {
                return redirect()->to('/admin/users')->with('error', 'Username is required');
            }

            $userData = [
                'username' => $username,
                'role'  => $this->normalizeRole($role, (string) ($user['role'] ?? 'cashier')),
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($this->userModel->update($userId, $userData)) {
                $isEditingSelf = (int) $userId === (int) session()->get('user_id');

                // Keep current session permissions aligned when editing own account.
                if ($isEditingSelf) {
                    session()->set('role', $userData['role']);
                    session()->set('is_admin', $userData['role'] === 'Admin');
                }

                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'edit_user',
                    "Updated user: {$userData['username']}"
                );

                if ($isEditingSelf && $userData['role'] !== 'Admin') {
                    return redirect()->to('/pos')->with('success', 'User updated. Your role is now Cashier.');
                }

                return redirect()->to('/admin/users')->with('success', 'User updated successfully');
            }

            return redirect()->to('/admin/users')->with('error', 'Failed to update user');
        }

        return redirect()->to('/admin/users');
    }

    // Delete user
    public function deleteUser($userId)
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Prevent deleting own account
        if ($userId == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Cannot delete your own account');
        }

        $user = $this->userModel->find($userId);

        if ($this->userModel->delete($userId)) {
            $this->activityLog->logActivity(
                session()->get('user_id'),
                'delete_user',
                'Deleted user: ' . ($user['username'] ?? ('ID ' . $userId))
            );

            return redirect()->to('/admin/users')->with('success', 'User deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete user');
    }

    // Send Daily Sales Report via Email
    public function sendDailySalesReport()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Get recipient email from POST or use admin's email
        $recipientEmail = $this->request->getPost('email');
        
        if (!$recipientEmail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please provide recipient email address'
            ]);
        }

        // Gather today's sales data
        $salesData = $this->gatherSalesData();
        
        // Send email using EmailService
        $result = $this->emailService->sendDailySalesReport($recipientEmail, $salesData);
        
        if ($result['success']) {
            $this->activityLog->logActivity(
                session()->get('user_id'),
                'send_daily_report',
                "Sent daily sales report to {$recipientEmail}"
            );
        }
        
        return $this->response->setJSON($result);
    }

    // Gather sales data for today
    private function gatherSalesData()
    {
        $today = date('Y-m-d');
        
        // Get today's orders
        $todayOrders = $this->orderModel
            ->where('DATE(created_at)', $today)
            ->findAll();
        
        $totalOrders = count($todayOrders);
        $completedOrders = count(array_filter($todayOrders, fn($o) => $o['status'] === 'completed'));
        $pendingOrders = count(array_filter($todayOrders, fn($o) => $o['status'] === 'pending'));
        
        // Calculate total revenue (exclude cancelled orders)
        $totalRevenue = array_sum(array_map(
            fn($o) => $o['status'] !== 'cancelled' ? $o['total_amount'] : 0,
            $todayOrders
        ));
        
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Get top selling items
        $topItems = $this->orderItemModel
            ->select('menu_items.name, SUM(order_items.quantity) as quantity, SUM(order_items.quantity * order_items.price) as revenue')
            ->join('menu_items', 'menu_items.id = order_items.menu_item_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('DATE(orders.created_at)', $today)
            ->groupBy('order_items.menu_item_id')
            ->orderBy('quantity', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Get payment methods summary
        $payments = $this->paymentModel
            ->select('payment_method, SUM(amount) as total')
            ->join('orders', 'orders.id = payments.order_id')
            ->where('DATE(payments.payment_date)', $today)
            ->groupBy('payment_method')
            ->findAll();
        
        $paymentMethods = [];
        foreach ($payments as $payment) {
            $paymentMethods[$payment['payment_method']] = $payment['total'];
        }
        
        return [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'top_items' => $topItems,
            'payment_methods' => $paymentMethods,
        ];
    }

    // View SMS logs from staff
    public function smsLogs()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $status = strtoupper((string) ($this->request->getGet('status') ?? ''));
        if (!in_array($status, ['', 'SENT', 'FAILED'], true)) {
            $status = '';
        }

        $dateFrom = (string) ($this->request->getGet('date_from') ?? '');
        $dateTo = (string) ($this->request->getGet('date_to') ?? '');
        $sort = (string) ($this->request->getGet('sort') ?? 'newest');
        if (!in_array($sort, ['newest', 'oldest', 'status', 'staff'], true)) {
            $sort = 'newest';
        }

        $filters = [
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'sort' => $sort,
        ];

        // Get filtered SMS logs with staff information
        $data['sms_logs'] = $this->smsLogModel->getFilteredLogsWithStaff($filters, 200);
        
        // Get statistics
        $data['statistics'] = $this->smsLogModel->getStatistics();
        $data['selected_status'] = $status;
        $data['selected_date_from'] = $dateFrom;
        $data['selected_date_to'] = $dateTo;
        $data['selected_sort'] = $sort;

        return view('admin/sms_logs', $data);
    }

}

