<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['order_number', 'status', 'total_amount'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'order_number' => 'required|is_unique[orders.order_number]',
        'status'       => 'required|in_list[pending,paid,completed,cancelled]',
        'total_amount' => 'required|decimal',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Get order with items
    public function getOrderWithItems($orderId)
    {
        $order = $this->find($orderId);
        if ($order) {
            $orderItemModel = new OrderItemModel();
            $order['items'] = $orderItemModel->getOrderItems($orderId);
        }
        return $order;
    }

    // Get order by order number
    public function getOrderByNumber($orderNumber)
    {
        return $this->where('order_number', $orderNumber)->first();
    }

    // Get orders by status
    public function getOrdersByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // Get today's orders
    public function getTodayOrders()
    {
        return $this->where('DATE(created_at)', date('Y-m-d'))
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // Get sales report
    public function getSalesReport($startDate, $endDate)
    {
        return $this->select('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total_amount) as total_sales')
                    ->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->where('status !=', 'cancelled')
                    ->groupBy('DATE(created_at)')
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }

    // Generate unique order number
    public function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return $prefix . $date . $random;
    }
}
