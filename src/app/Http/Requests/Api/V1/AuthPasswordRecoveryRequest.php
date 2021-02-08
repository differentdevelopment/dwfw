<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\BaseApiFormRequest;

class AuthPasswordRecoveryRequest extends BaseApiFormRequest
{

    public function authorize(): bool
    {
        // here comes oauth2
        // https://laravel.com/docs/7.x/validation#authorizing-form-requests
        return true;
    }

    public function attributes()
    {
        return [
            'password' => __('api.password'),
            'password_confirmation' => __('api.password_confirmation'),
        ];
    }

    public function rules(): array
    {
        return $this->getBaseDataRules() + [
                'email' => [
                    'required',
                    'string',
                    'email',
                ],
                'hash' => [
                    'required',
                    'string',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                ],
                'password_confirmation' => [
                    'required',
                    'string',
                ],
            ];
    }
}
