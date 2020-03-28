<?php

namespace App\Http\UserInterfaces\SettingAttrDivsOrder\Update;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\InputData;

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
            'divisions' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'divisions.required' => '属性は必須です。',
        ];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('divisions')
        );
    }
}
