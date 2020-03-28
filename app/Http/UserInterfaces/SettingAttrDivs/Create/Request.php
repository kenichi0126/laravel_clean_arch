<?php

namespace App\Http\UserInterfaces\SettingAttrDivs\Create;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\InputData;

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
            'sumpleName' => 'required|max:20|str_in_comma_or_doublequote',
        ];
    }

    public function messages()
    {
        return [
            'sumpleName.required' => 'サンプル名を入力してください。',
            'sumpleName.max' => 'サンプル名は20文字以内で入力してください。',
            'sumpleName.str_in_comma_or_doublequote' => 'サンプル名にカンマとダブルクォーテーションは利用できません。',
        ];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('info'),
            $this->input('regionId'),
            $this->input('sumpleName'),
            \Auth::id()
        );
    }
}
