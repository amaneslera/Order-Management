<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockAlertsTable extends Migration
{
    public function up()
    {
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
            'alert_type' => [
                'type'       => 'ENUM',
                'constraint' => ['low_stock', 'out_of_stock'],
                'default'    => 'low_stock',
            ],
            'current_stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'threshold' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sent_sms' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'sent_email' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('menu_item_id');
        $this->forge->addKey('alert_type');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['alert_type', 'created_at']);
        $this->forge->addKey(['sent_sms', 'created_at']);
        $this->forge->addForeignKey('menu_item_id', 'menu_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_alerts', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_alerts', true);
    }
}
