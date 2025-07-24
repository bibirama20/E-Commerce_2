<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'total', 'city', 'shipping_cost',
        'shipping_delivery', 'estimasi', 'nama', 'alamat', 'no_hp',
        'status', 'created_at', 'updated_at' // ✅ tambahkan ini
    ];
    protected $useTimestamps = true;
}
