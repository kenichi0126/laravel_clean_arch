<?php

namespace App\Http\UserInterfaces\Setting\Save;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Setting\Save\UseCases\InputData;

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
            'secFlag' => 'required|int',
            'division' => 'required|string',
            'regionId' => 'required|int',
        ];
    }

    public function messages()
    {
        return [
            'division.required' => '属性は必須です。',
        ];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('secFlag'),
            $this->input('division'),
            $this->input('codes'),
            $this->input('regionId'),
            \Auth::id()
        );
    }
}
