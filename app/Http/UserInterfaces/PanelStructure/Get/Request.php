<?php

namespace App\Http\UserInterfaces\PanelStructure\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\InputData;

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
        ];
    }

    public function messages()
    {
        return [
        ];
    }

    public function passedValidation(): void
    {
        $fiveDivisions = [
            'ga8',
            'ga12',
            'ga10s',
            'gm',
            'oc',
        ];
        $isBaseFiveDivision = (array_search($this->input('division'), $fiveDivisions) !== false) ? true : false;

        $userID = \UserInfo::execute(\Auth::id())->id;

        $this->inputData = new InputData(
            $this->input('division'),
            $this->input('regionId'),
            $isBaseFiveDivision,
            $userID
        );
    }
}
