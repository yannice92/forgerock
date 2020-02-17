<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */
namespace App\Forgerock\Request;

use App\Forgerock\Request\FormRequest;
use Illuminate\Validation\Validator;

class GetAccessToken extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required',
        ];
    }


    protected function getValidatorInstance(): \Illuminate\Contracts\Validation\Validator
    {
        $this->getInputSource()->replace($this->modifyData());
        $validator = parent::getValidatorInstance();

        return $validator;
    }

    protected function modifyData()
    {
        $data = $this->validationData();
        return $data;
    }
}
