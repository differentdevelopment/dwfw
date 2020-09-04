<?php

namespace Different\Dwfw\app\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class BaseDwfwApiFormRequest extends FormRequest
{

    /**
     * Override failedValidation for API
     * No redirect on error
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        self::throwError($validator->errors());
    }

    /**
     * Throw a new response exception for API
     * @param $error_messages
     */
    public static function throwError($error_messages)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => true,
                'message' => $error_messages
            ], JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
