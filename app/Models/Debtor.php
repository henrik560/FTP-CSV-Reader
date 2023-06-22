<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    use HasFactory;

    protected $fillable = [
        "debtor_number",
        "name_1",
        "name_2",
        "search_name",
        "address",
        "postalcode",
        "city",
        "country",
        "contact",
        "phonenumber",
        "mobile",
        "email",
        "email_cc",
        "email_invoice",
        "email_invoice_cc",
        "tax_number",
    ];
}
