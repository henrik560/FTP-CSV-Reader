<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtorProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        "debtor_number",
        "product_number",
        "sale",
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'group', 'product_number');
    }
}
