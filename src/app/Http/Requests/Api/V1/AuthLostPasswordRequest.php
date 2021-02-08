<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\BaseApiFormRequest;

class AuthLostPasswordRequest extends BaseApiFormRequest
{

    public function authorize(): bool
    {
        // here comes oauth2
        // https://laravel.com/docs/7.x/validation#authorizing-form-requests
        return true;
    }

    public function rules(): array
    {
        return $this->getBaseDataRules() + [
            'email' => [
                'required',
                'string',
                'email',
            ],
        ];
    }
}
