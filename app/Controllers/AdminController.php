<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class AdminController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $paymentModel;
    protected $userModel;
    protected $activityLog;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel = new PaymentModel();
        $this->userModel = new UserModel();
        $this->activityLog = new ActivityLogModel();
    }

    // Check if user is admin
    private function checkAuth()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['admin', 'Admin'])) {
            return redirect()->to('/login');
        }
        return null;
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

        // Set default date range
        if (!$startDate || !$endDate) {
            switch ($reportType) {
                case 'daily':
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d');
                    break;
                case 'weekly':
                    $startDate = date('Y-m-d', strtotime('-7 days'));
                    $endDate = date('Y-m-d');
                    break;
                case 'monthly':
                    $startDate = date('Y-m-01');
                    $endDate = date('Y-m-t');
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

        return view('admin/reports', $data);
    }

    // Activity logs
    public function activityLogs()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $data['logs'] = $this->activityLog->getLogsWithUsers(100);

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

        if ($this->request->getMethod() === 'post') {
            $userData = [
                'name'     => $this->request->getPost('name'),
                'email'    => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'     => $this->request->getPost('role'),
            ];

            if ($this->userModel->insert($userData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'add_user',
                    "Added new user: {$userData['email']}"
                );
                
                return redirect()->to('/admin/users')->with('success', 'User added successfully');
            }

            return redirect()->back()->with('error', 'Failed to add user');
        }

        return view('admin/add_user');
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

        if ($this->request->getMethod() === 'post') {
            $userData = [
                'name'  => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'role'  => $this->request->getPost('role'),
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($this->userModel->update($userId, $userData)) {
                $this->activityLog->logActivity(
                    session()->get('user_id'),
                    'edit_user',
                    "Updated user: {$userData['email']}"
                );

                return redirect()->to('/admin/users')->with('success', 'User updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update user');
        }

        $data['user'] = $user;
        return view('admin/edit_user', $data);
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
                "Deleted user: {$user['email']}"
            );

            return redirect()->to('/admin/users')->with('success', 'User deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete user');
    }
}
