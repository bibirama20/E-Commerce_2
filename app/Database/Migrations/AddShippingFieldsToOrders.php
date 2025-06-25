<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShippingFieldsToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'shipping_delivery' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'after'      => 'shipping_cost'
            ],
            'estimasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'after'      => 'shipping_delivery'
            ],
            'total' => [
                'type' => 'INT',
                'after' => 'estimasi'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', ['shipping_delivery', 'estimasi', 'total']);
    }
}
