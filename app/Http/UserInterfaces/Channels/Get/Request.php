<?php

namespace App\Http\UserInterfaces\Channels\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Channels\Get\UseCases\InputData;
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
            'division' => 'required|alpha_num',
            'regionId' => 'integer',
            'withCommercials' => 'boolean',
        ];
    }

    public function messages()
    {
        return [];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('division'),
            $this->input('regionId'),
            $this->input('withCommercials')
        );
    }
}
