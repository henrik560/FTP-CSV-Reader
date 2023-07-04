<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordService
{
    public function generate(): string
    {
        return Str::password(env("USER_PASSWORD_LENGTH", 12));
    }
}
