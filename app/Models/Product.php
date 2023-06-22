<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_number',
        'oms_1',
        'oms_2',
        'oms_3',
        'search_name',
        'group',
        'ean_number',
        'sell_price',
        'unit',
        'unit_price',
        'stock',
    ];
}
