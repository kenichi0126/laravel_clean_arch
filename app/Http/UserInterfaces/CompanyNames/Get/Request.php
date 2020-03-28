<?php

namespace App\Http\UserInterfaces\CompanyNames\Get;

use App\Rules\SearchableBoundaryValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\InputData;

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
            'startDateTime' => 'required|date',
            'endDateTime' => 'required|date',
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
    }

    public function messages()
    {
        return [
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge($this->SearchableBoundaryValidatorField());
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('companyName'),
            $this->input('progIds'),
            $this->input('regionId'),
            $this->input('companyId'),
            $this->input('channels'),
            $this->input('cmType'),
            $this->input('cmSeconds'),
            $this->input('productIds'),
            $this->input('dataType')
        );
    }
}
