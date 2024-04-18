<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankDetailsRequest extends BaseFormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'accountName' => 'string|required',
            'accountNumber' => 'required|numeric|unique:bank_details',
            'bankName' => 'string|required',
            'BVN' => 'required|numeric|digits:11|unique:bank_details'
        ];
    }
}
