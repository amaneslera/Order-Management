<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['order_id', 'menu_item_id', 'quantity', 'price', 'addons', 'notes'];

    // Validation
    protected $validationRules      = [
        'order_id'     => 'required|integer',
        'menu_item_id' => 'required|integer',
        'quantity'     => 'required|integer|greater_than[0]',
        'price'        => 'required|decimal',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Get order items with menu details
    public function getOrderItems($orderId)
    {
        return $this->select('order_items.*, menu_items.name, menu_items.category, menu_items.image')
                    ->join('menu_items', 'menu_items.id = order_items.menu_item_id')
                    ->where('order_items.order_id', $orderId)
                    ->findAll();
    }

    // Get total amount for order
    public function getOrderTotal($orderId)
    {
        $result = $this->select('SUM(quantity * price) as total')
                       ->where('order_id', $orderId)
                       ->first();
        return $result['total'] ?? 0;
    }

    // Get top selling items
    public function getTopSellingItems($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('order_items')
                            ->select('menu_items.id, menu_items.name, menu_items.category, menu_items.image, 
                                      SUM(order_items.quantity) as total_quantity, 
                                      SUM(order_items.quantity * order_items.price) as total_revenue')
                            ->join('menu_items', 'menu_items.id = order_items.menu_item_id')
                            ->join('orders', 'orders.id = order_items.order_id')
                            ->where('orders.status !=', 'cancelled')
                            ->groupBy('menu_items.id, menu_items.name, menu_items.category, menu_items.image')
                            ->orderBy('total_quantity', 'DESC')
                            ->limit($limit);

        if ($startDate && $endDate) {
            // Ensure end date includes the entire day (23:59:59)
            $endDateTime = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            
            $builder->where('orders.created_at >=', $startDate)
                    ->where('orders.created_at <=', $endDateTime);
        }

        return $builder->get()->getResultArray();
    }
}
