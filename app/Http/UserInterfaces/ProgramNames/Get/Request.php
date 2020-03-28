<?php

namespace App\Http\UserInterfaces\ProgramNames\Get;

use App\Rules\SearchableBoundaryValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\InputData;

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
            $this->input('programName'),
            $this->input('channels'),
            $this->input('digitalAndBs'),
            $this->input('programFlag'),
            $this->input('digitalKanto'),
            $this->input('bs1'),
            $this->input('bs2'),
            $this->input('cmType'),
            $this->input('cmSeconds'),
            $this->input('productIds'),
            $this->input('companies'),
            $this->input('regionId'),
            $this->input('dataType'),
            $this->input('programIds'),
            $this->input('wdays'),
            $this->input('holiday')
        );
    }
}
