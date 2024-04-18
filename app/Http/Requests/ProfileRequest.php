<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends BaseFormRequest
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
            'first_name'        => 'nullable|string|max:255',
            'last_name'         => 'nullable|string|max:255',
            'address'           => 'nullable|string',
            'country'           => 'nullable|string|max:255',
            'industry'          => 'nullable|string|max:255',
            'state'             => 'nullable|string|max:255',
            'city'              => 'nullable|string|max:255',
            'zip_code'          => 'nullable|string|max:255',
            'gender'            => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:255',
            'date_of_birth'     => 'nullable|string',
            'profession'        => 'nullable|string|max:255',
            'business_name'     => 'nullable|string|max:255',
            'office_number'     => 'nullable|string|max:255',
            'residential_number'=> 'nullable|string|max:255',
            'profile_picture'   => 'nullable|image',
            'description'       => 'nullable|string|max:255',
    ];
}

}
