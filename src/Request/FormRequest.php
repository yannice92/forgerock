<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Request;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Pearl\RequestValidate\RequestAbstract;

class FormRequest extends RequestAbstract
{
    protected function validationData(): array
    {
        $data = $this->merge($this->all());
        return $data->request->all();
    }


    protected function formatErrors(Validator $validator):JsonResponse
    {
        $return['status']           = false;
        $return['code']             = 401;
        $return['message']          = $validator->getMessageBag()->first();
        $return['error_message']    = $validator->getMessageBag()->first();
        $return['data']             = null;

        return new JsonResponse($return, 422);
    }
}

