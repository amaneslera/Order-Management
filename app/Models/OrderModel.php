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
        // Ensure end date includes the entire day (23:59:59)
        $endDateTime = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
        
        return $this->select('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total_amount) as total_sales')
                    ->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDateTime)
                    ->where('status !=', 'cancelled')
                    ->groupBy('DATE(created_at)')
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }

    // Generate unique order number
    public function generateOrderNumber()
    {
        // Format: LNNN (e.g., A001, B234, Z999)
        // 1 letter + 3 digits = 4 characters total
        // Supports 26,000 orders (A000-Z999)
        
        // Get the latest order to determine next sequence
        $latestOrder = $this->orderBy('id', 'DESC')->first();
        
        if ($latestOrder && preg_match('/^([A-Z])(\d{3})$/', $latestOrder['order_number'], $matches)) {
            $letter = $matches[1];
            $number = (int) $matches[2];
            
            // Increment number
            $number++;
            
            // If number exceeds 999, move to next letter
            if ($number > 999) {
                $number = 0;
                $letter = chr(ord($letter) + 1);
                
                // If letter exceeds Z, wrap to A (though 26,000 orders is unlikely)
                if ($letter > 'Z') {
                    $letter = 'A';
                }
            }
        } else {
            // Start with A000
            $letter = 'A';
            $number = 0;
        }
        
        // Format as LNNN (e.g., A001)
        $orderNumber = $letter . str_pad($number, 3, '0', STR_PAD_LEFT);
        
        // Safety check: ensure uniqueness (in case of race conditions)
        $attempts = 0;
        while ($this->where('order_number', $orderNumber)->first() && $attempts < 100) {
            $number++;
            if ($number > 999) {
                $number = 0;
                $letter = chr(ord($letter) + 1);
                if ($letter > 'Z') {
                    $letter = 'A';
                }
            }
            $orderNumber = $letter . str_pad($number, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }
        
        return $orderNumber;
    }
}
