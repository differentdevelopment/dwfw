<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\BaseApiFormRequest;
use Different\Dwfw\app\Traits\LoggableApi;

class AuthRegisterRequest extends BaseApiFormRequest
{
    use LoggableApi;

    public function authorize(): bool
    {
        // here comes oauth2
        // https://laravel.com/docs/7.x/validation#authorizing-form-requests
        return true;
    }

    public function failedValidation($validator)
    {
        $email_errors = $validator->errors()->get('email');
        $input = $this->input();
        if(!empty($email_errors)) {
            $this->log('EMAIL_ERROR', null, json_encode([$input['email'], $email_errors]), null, null);
        }
        parent::failedValidation($validator);
    }

    public function rules(): array
    {
        return $this->getBaseDataRules() + [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:60',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'unique:users',
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
                'min:6',
            ],
        ];
    }
}
