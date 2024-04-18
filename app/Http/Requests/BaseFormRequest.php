<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{


    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();
        $transformed = [];
        foreach ($errors as $field => $messages) {
            $transformed[$field] = $messages[0];
        }
        $response = response()->json([
            'status' => 'error',
            'message' => $transformed,
        ], 422);

        throw new HttpResponseException($response);
    }

}
