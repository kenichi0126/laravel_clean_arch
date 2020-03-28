<?php

namespace App\Http\UserInterfaces\SearchConditions\Create;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\InputData;

/**
 * Class Request.
 */
final class Request extends FormRequest
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
            'regionId' => 'required|integer',
            'name' => 'required|max:50',
            'routeName' => 'required|max:255',
            'condition' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'regionId.required' => 'リージョンIDは必須です。',
            'regionId.integer' => 'リージョンIDは数値です。',
            'name.required' => '名前は必須です。',
            'name.max' => '名前は最大50文字までです。',
            'routeName.required' => 'ルート名は必須です。',
            'routeName.max' => 'ルート名は最大255文字までです。',
            'condition.required' => '条件は必須です。',
        ];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            \Auth::id(),
            $this->input('regionId'),
            $this->input('name'),
            $this->input('routeName'),
            $this->input('condition')
        );
    }
}
