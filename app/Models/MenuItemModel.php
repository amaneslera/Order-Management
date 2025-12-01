<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuItemModel extends Model
{
    protected $table            = 'menu_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'category', 'description', 'price', 'stock_quantity', 'low_stock_threshold', 'image', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'     => 'required|min_length[3]|max_length[255]',
        'category' => 'required',
        'price'    => 'required|decimal',
        'status'   => 'required|in_list[available,unavailable]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Get available menu items
    public function getAvailableItems()
    {
        return $this->where('status', 'available')->findAll();
    }

    // Get items by category
    public function getItemsByCategory($category)
    {
        return $this->where('category', $category)
                    ->where('status', 'available')
                    ->findAll();
    }

    // Get all categories
    public function getCategories()
    {
        return $this->distinct()
                    ->select('category')
                    ->findAll();
    }

    // Get low stock items
    public function getLowStockItems()
    {
        return $this->where('stock_quantity <=', $this->db->table('menu_items')->selectMax('low_stock_threshold'))
                    ->where('stock_quantity < low_stock_threshold')
                    ->where('status', 'available')
                    ->orderBy('stock_quantity', 'ASC')
                    ->findAll();
    }

    // Get out of stock items
    public function getOutOfStockItems()
    {
        return $this->where('stock_quantity', 0)
                    ->where('status', 'available')
                    ->findAll();
    }

    // Update stock quantity
    public function updateStock($itemId, $newQuantity)
    {
        return $this->update($itemId, ['stock_quantity' => $newQuantity]);
    }

    // Deduct stock
    public function deductStock($itemId, $quantity)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }

        $newStock = max(0, $item['stock_quantity'] - $quantity);
        return $this->update($itemId, ['stock_quantity' => $newStock]);
    }

    // Add stock
    public function addStock($itemId, $quantity)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }

        $newStock = $item['stock_quantity'] + $quantity;
        return $this->update($itemId, ['stock_quantity' => $newStock]);
    }

    // Check if item has sufficient stock
    public function hasSufficientStock($itemId, $requiredQuantity)
    {
        $item = $this->find($itemId);
        return $item && $item['stock_quantity'] >= $requiredQuantity;
    }
}
