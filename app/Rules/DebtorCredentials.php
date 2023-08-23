<?php

namespace App\Rules;

use App\Models\Debtor;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class DebtorCredentials implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $email = request()->input('email');
        $password = request()->input('password');

        $debtor = Debtor::where('email', $email)->first();

        if (!$debtor) {
            return false; // Email doesn't exist in the debtor model
        }

        return Hash::check($password, $debtor->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid email or password!';
    }
}
