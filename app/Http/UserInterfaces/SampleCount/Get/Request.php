<?php

namespace App\Http\UserInterfaces\SampleCount\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\InputData;

class Request extends FormRequest
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
            'info' => 'array',
            'conditionCross' => 'required|array',
            'regionId' => 'required|int',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('info'),
            $this->input('conditionCross'),
            $this->input('regionId'),
            $this->input('editFlg')
        );
    }
}
