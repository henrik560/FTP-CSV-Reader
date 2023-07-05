<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDebtorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'debtor_number' => 'required',
            'name_1' => 'required',
            'name_2' => 'required',
            'search_name' => 'required',
            'address' => 'required',
            'postalcode' => 'required',
            'city' => 'required',
            'country' => 'required',
            'contact' => 'required',
            'phonenumber' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'email_cc' => 'required',
            'email_invoice' => 'required',
            'email_invoice_cc' => 'required',
            'tax_number' => 'required',
            'password' => 'required',
        ];
    }
}
