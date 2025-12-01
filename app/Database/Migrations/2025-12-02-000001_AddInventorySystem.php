<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInventorySystem extends Migration
{
    public function up()
    {
        // Add stock management columns to menu_items table
        $fields = [
            'stock_quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'price',
            ],
            'low_stock_threshold' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 10,
                'after'      => 'stock_quantity',
            ],
        ];
        
        $this->forge->addColumn('menu_items', $fields);

        // Create inventory_logs table for tracking all stock changes
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'menu_item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'action' => [
                'type'       => 'ENUM',
                'constraint' => ['add', 'deduct', 'adjust', 'initial'],
                'default'    => 'adjust',
            ],
            'quantity_change' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'previous_stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'new_stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'order_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('menu_item_id');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('menu_item_id', 'menu_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inventory_logs');
    }

    public function down()
    {
        // Remove inventory_logs table
        $this->forge->dropTable('inventory_logs', true);

        // Remove stock columns from menu_items
        $this->forge->dropColumn('menu_items', ['stock_quantity', 'low_stock_threshold']);
    }
}
