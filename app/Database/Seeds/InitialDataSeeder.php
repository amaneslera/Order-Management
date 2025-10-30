<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Insert sample menu items - Coffee
        $menuItems = [
            [
                'name'        => 'Espresso',
                'category'    => 'Coffee',
                'description' => 'Strong and bold espresso shot',
                'price'       => 80.00,
                'image'       => 'espresso.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Americano',
                'category'    => 'Coffee',
                'description' => 'Espresso with hot water',
                'price'       => 95.00,
                'image'       => 'americano.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Cappuccino',
                'category'    => 'Coffee',
                'description' => 'Espresso with steamed milk and foam',
                'price'       => 120.00,
                'image'       => 'cappuccino.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Latte',
                'category'    => 'Coffee',
                'description' => 'Espresso with steamed milk',
                'price'       => 130.00,
                'image'       => 'latte.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Mocha',
                'category'    => 'Coffee',
                'description' => 'Chocolate-flavored latte',
                'price'       => 140.00,
                'image'       => 'mocha.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Caramel Macchiato',
                'category'    => 'Coffee',
                'description' => 'Vanilla latte with caramel drizzle',
                'price'       => 150.00,
                'image'       => 'caramel_macchiato.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            // Snacks
            [
                'name'        => 'Chocolate Chip Cookie',
                'category'    => 'Snacks',
                'description' => 'Freshly baked chocolate chip cookie',
                'price'       => 45.00,
                'image'       => 'cookie.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Blueberry Muffin',
                'category'    => 'Snacks',
                'description' => 'Soft and fluffy blueberry muffin',
                'price'       => 65.00,
                'image'       => 'muffin.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Croissant',
                'category'    => 'Snacks',
                'description' => 'Buttery and flaky croissant',
                'price'       => 75.00,
                'image'       => 'croissant.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Ham & Cheese Sandwich',
                'category'    => 'Snacks',
                'description' => 'Toasted sandwich with ham and cheese',
                'price'       => 95.00,
                'image'       => 'sandwich.jpg',
                'status'      => 'available',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('menu_items')->insertBatch($menuItems);
    }
}
