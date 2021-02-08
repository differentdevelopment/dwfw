<?php

namespace App\Http\Requests\Api;

use Different\Dwfw\app\Http\Requests\Api\BaseDwfwApiFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class BaseApiFormRequest extends BaseDwfwApiFormRequest
{
    /**
     * Base rules for all base data types
     * @return array
     */
    public function getBaseDataRules(): array
    {
        return [

        ];
    }

    public function getBaseAttributeNames(): array
    {
        return [

        ];
    }

    public function getAuthorization(String $entity_type, String $key){
        $entity = $entity_type::find($this->route($key)->id);

        return $entity && $this->user()->can('update', $entity);
    }
}
