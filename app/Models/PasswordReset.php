<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'debtor_id',
        'token'
    ];

    public function debtor()
    {
        return $this->hasOne(Debtor::class, 'id', 'debtor_id');
    }
}
