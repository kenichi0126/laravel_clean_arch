<?php

namespace App\Http\UserInterfaces\ProgramTableDetail\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\InputData;

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
            'progId' => 'required',
            'timeBoxId' => 'required',
            'division' => 'required',
            'regionId' => 'required',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('regionId'),
            $this->input('division'),
            $this->input('progId'),
            $this->input('timeBoxId'),
            \Config::get('const.DWH_PERIOD_DATE'),
            \Config::get('const.DWH_PERIOD_BOUNDARY')
        );
    }
}
