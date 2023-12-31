<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtorNetto extends Model
{
    use HasFactory;

    protected $fillable = [
        'debtor_number',
        'product_number',
        'type',
        'pbk',
    ];
}
