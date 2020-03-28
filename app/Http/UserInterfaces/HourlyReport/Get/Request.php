<?php

namespace App\Http\UserInterfaces\HourlyReport\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\HourlyReport\Get\UseCases\InputData;

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
            'regionId' => 'required|integer',
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
            \Auth::getUser()->sponsor->sponsorTrial->settings
        );
    }
}
