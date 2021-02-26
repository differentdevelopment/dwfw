<?php

namespace Different\Dwfw\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountChangeRequest extends FormRequest
{
    public function authorize()
    {
        //Don't know why second part needed, it is already registered for every permission check - Urudin
        return backpack_user()->hasPermissionTo('change account') || backpack_user()->hasRole('super admin');
    }

    public function rules()
    {
        return [
            'account_id' => 'required|numeric',
        ];
    }
}
