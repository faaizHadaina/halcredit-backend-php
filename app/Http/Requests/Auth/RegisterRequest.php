<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends BaseFormRequest
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
        'name' => 'required|string',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:6|max:20',
    ];
}

public function messages()
{
    return [
        'name.required' => 'Name is required',
        'email.required' => 'Email is required',
        'email.email' => 'This has to be a valid email',
        'email.unique' => 'This email already exists.',
        'password.required' => 'Password field is required',
        'password.min' => 'Your password has to be a minimum of 6 characters',
        'password.max' => 'The allowable length of the password is 20',
    ];
}

}
