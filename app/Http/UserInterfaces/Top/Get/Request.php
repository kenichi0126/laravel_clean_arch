<?php

namespace App\Http\UserInterfaces\Top\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Top\Get\UseCases\InputData;

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
        return [];
    }

    public function messages()
    {
        return [];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('regionId'),
            \Config::get('const.CHANNEL_COLORS'),
            \Config::get('const.CHANNEL_COLORS_KANSAI')
        );
    }
}
