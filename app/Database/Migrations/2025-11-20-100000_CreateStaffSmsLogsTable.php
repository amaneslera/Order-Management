<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffSmsLogsTable extends Migration
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
            'staff_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'staff_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'admin_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['SENT', 'FAILED'],
                'default'    => 'FAILED',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sms_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'SMS API message ID',
            ],
            'sent_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('staff_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        
        // Add foreign key to users table
        $this->forge->addForeignKey('staff_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('staff_sms_logs');
    }

    public function down()
    {
        $this->forge->dropTable('staff_sms_logs');
    }
}
