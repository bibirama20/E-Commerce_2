<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'price', 'stock', 'description', 'image', 'diskon', 'weight'];
}
