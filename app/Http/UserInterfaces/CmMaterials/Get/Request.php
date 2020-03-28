<?php

namespace App\Http\UserInterfaces\CmMaterials\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;

class Request extends FormRequest
{
    use ValidatorSetterTrait;

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
            'product_ids' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'start_time_hour' => 'integer',
            'start_time_min' => 'integer',
            'end_time_hour' => 'integer',
            'end_time_min' => 'integer',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('product_ids'),
            $this->input('start_date'),
            $this->input('end_date'),
            $this->input('start_time_hour'),
            $this->input('start_time_min'),
            $this->input('end_time_hour'),
            $this->input('end_time_min'),
            $this->input('regionId'),
            $this->input('channels'),
            $this->input('cmType'),
            $this->input('cmSeconds'),
            $this->input('companyIds'),
            $this->input('progIds')
        );
    }
}
