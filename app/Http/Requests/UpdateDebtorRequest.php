<?php

namespace App\Http\Requests;

use App\Rules\DebtorCredentials;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name_1'            => 'nullable',
            'name_2'            => 'nullable',
            'search_name'       => 'nullable',
            'postalcode'        => 'nullable',
            'address'           => 'nullable',
            'city'              => 'nullable',
            'country'           => 'nullable',
            'contact'           => 'nullable',
            'phonenumber'       => 'nullable',
            'mobile'            => 'nullable',
            'email'             => ['required', 'email', new DebtorCredentials()],
            'passsword'         => 'required',
            'email_cc'          => 'nullable',
            'email_invoice'     => 'nullable',
            'email_invoice_cc'  => 'nullable',
            'tax_number'        => 'nullable',
        ];
    }
}
