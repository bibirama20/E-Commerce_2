<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'order_id', 'product_id', 'quantity', 'price', 'subtotal'
    ];

    // Tambahkan ini untuk menghindari error terkait updated_at
    protected $useTimestamps = false;
}
