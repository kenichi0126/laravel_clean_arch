<?php

namespace App\Http\UserInterfaces\SearchConditions\Delete;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\InputData;

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
            'id' => 'required',
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
            'id.required' => 'IDは必須です。',
        ];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('id')
        );
    }
}
