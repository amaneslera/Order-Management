<?php
// send_weekly_digest.php
// Script to send weekly sales digest email (for scheduled task)

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/Paths.php';

$paths = new Config\Paths();
require_once SYSTEMPATH . 'bootstrap.php';

use App\Libraries\EmailService;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;

$emailService = new EmailService();
$orderModel = new OrderModel();
$orderItemModel = new OrderItemModel();
$paymentModel = new PaymentModel();

// Get last week's date range (Monday to Sunday)
$lastMonday = date('Y-m-d', strtotime('last monday', strtotime('tomorrow')));
$lastSunday = date('Y-m-d', strtotime($lastMonday . ' +6 days'));


// Gather weekly sales data
$weeklyOrders = $orderModel->where('DATE(created_at) >=', $lastMonday)
    ->where('DATE(created_at) <=', $lastSunday)
    ->findAll();

$totalOrders = count($weeklyOrders);
$totalRevenue = array_sum(array_column($weeklyOrders, 'total_amount'));

// Completed and pending orders
$completedOrders = 0;
$pendingOrders = 0;
// Staff performance aggregation
$staffPerformance = [];
foreach ($weeklyOrders as $order) {
    if ($order['status'] === 'completed') $completedOrders++;
    if ($order['status'] === 'pending') $pendingOrders++;
    // Aggregate by staff (created_by or user_id)
    $staffId = $order['created_by'] ?? $order['user_id'] ?? null;
    if ($staffId) {
        if (!isset($staffPerformance[$staffId])) {
            $staffPerformance[$staffId] = [
                'orders' => 0,
                'sales' => 0,
                'username' => null,
            ];
        }
        $staffPerformance[$staffId]['orders']++;
        $staffPerformance[$staffId]['sales'] += $order['total_amount'];
    }
}
$averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

// Attach usernames to staff performance
if (!empty($staffPerformance)) {
    $userModel = new \App\Models\UserModel();
    foreach ($staffPerformance as $staffId => &$perf) {
        $user = $userModel->find($staffId);
        $perf['username'] = $user['username'] ?? ('User #' . $staffId);
    }
    unset($perf);
}

// Top selling items
$topItems = $orderItemModel->getTopSellingItems(5, $lastMonday, $lastSunday);
$topItemsArr = [];
foreach ($topItems as $item) {
    $topItemsArr[] = [
        'name' => $item['name'],
        'quantity' => $item['total_quantity'],
        'revenue' => $item['total_revenue'],
    ];
}

// Payment methods summary
$paymentMethods = $paymentModel->getPaymentMethodsSummary($lastMonday, $lastSunday);
$paymentMethodsArr = [];
foreach ($paymentMethods as $method) {
    $paymentMethodsArr[$method['payment_method']] = $method['total'];
}

$salesData = [
    'total_orders' => $totalOrders,
    'completed_orders' => $completedOrders,
    'pending_orders' => $pendingOrders,
    'total_revenue' => $totalRevenue,
    'average_order_value' => $averageOrderValue,
    'top_items' => $topItemsArr,
    'payment_methods' => $paymentMethodsArr,
    'period_label' => date('M d', strtotime($lastMonday)) . ' - ' . date('M d, Y', strtotime($lastSunday)),
    'staff_performance' => array_values($staffPerformance),
];

// Recipient (for now, use admin email from .env)
// Support multiple recipients (comma-separated in .env: email.recipients)
$recipients = getenv('email.recipients') ?: getenv('email.fromEmail');
if (!$recipients) {
    echo "No recipients configured. Set email.recipients or email.fromEmail in .env\n";
    exit(1);
}
$recipientList = array_map('trim', explode(',', $recipients));
$allSuccess = true;
foreach ($recipientList as $recipientEmail) {
    if (!$recipientEmail) continue;
    $result = $emailService->sendWeeklyDigest($recipientEmail, $salesData);
    if ($result['success']) {
        echo "Weekly digest sent successfully to $recipientEmail!\n";
    } else {
        echo "Error sending to $recipientEmail: " . $result['message'] . "\n";
        $allSuccess = false;
    }
}
exit($allSuccess ? 0 : 1);
