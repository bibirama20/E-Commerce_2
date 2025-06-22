<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders'; // nama tabel di database
    protected $primaryKey = 'id';

    protected $allowedFields = [
    'user_id', 'total', 'city', 'shipping_cost',
    'nama', 'alamat', 'no_hp' // <--- tambahkan ini
];
}
