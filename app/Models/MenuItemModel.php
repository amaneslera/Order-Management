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
    protected $allowedFields    = ['name', 'category', 'description', 'price', 'image', 'status'];

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
}
