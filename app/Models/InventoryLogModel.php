<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryLogModel extends Model
{
    protected $table            = 'inventory_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'menu_item_id', 'action', 'quantity_change', 'previous_stock', 
        'new_stock', 'order_id', 'user_id', 'notes', 'created_at'
    ];

    // Validation
    protected $validationRules      = [
        'menu_item_id'    => 'required|integer',
        'action'          => 'required|in_list[add,deduct,adjust,initial]',
        'quantity_change' => 'required|integer',
        'previous_stock'  => 'required|integer',
        'new_stock'       => 'required|integer',
        'user_id'         => 'required|integer',
    ];
    
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Log inventory change
     */
    public function logInventoryChange($menuItemId, $action, $quantityChange, $previousStock, $newStock, $userId, $orderId = null, $notes = null)
    {
        $data = [
            'menu_item_id'    => $menuItemId,
            'action'          => $action,
            'quantity_change' => $quantityChange,
            'previous_stock'  => $previousStock,
            'new_stock'       => $newStock,
            'order_id'        => $orderId,
            'user_id'         => $userId,
            'notes'           => $notes,
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data);
    }

    /**
     * Get inventory logs with menu item and user details
     */
    public function getLogsWithDetails($limit = 100)
    {
        return $this->select('inventory_logs.*, menu_items.name as item_name, users.username')
                    ->join('menu_items', 'menu_items.id = inventory_logs.menu_item_id')
                    ->join('users', 'users.id = inventory_logs.user_id')
                    ->orderBy('inventory_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs for specific menu item
     */
    public function getItemLogs($menuItemId, $limit = 50)
    {
        return $this->select('inventory_logs.*, users.username')
                    ->join('users', 'users.id = inventory_logs.user_id')
                    ->where('inventory_logs.menu_item_id', $menuItemId)
                    ->orderBy('inventory_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get inventory activity for date range
     */
    public function getActivityReport($startDate, $endDate)
    {
        return $this->select('inventory_logs.*, menu_items.name as item_name, users.username')
                    ->join('menu_items', 'menu_items.id = inventory_logs.menu_item_id')
                    ->join('users', 'users.id = inventory_logs.user_id')
                    ->where('DATE(inventory_logs.created_at) >=', $startDate)
                    ->where('DATE(inventory_logs.created_at) <=', $endDate)
                    ->orderBy('inventory_logs.created_at', 'DESC')
                    ->findAll();
    }
}
