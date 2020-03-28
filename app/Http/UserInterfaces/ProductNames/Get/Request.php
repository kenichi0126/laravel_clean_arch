<?php

namespace App\Http\UserInterfaces\ProductNames\Get;

use App\Rules\SearchableBoundaryValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProductNames\Get\UseCases\InputData;

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
        // TODO: takata/Validationで必要なため配列からintのフィールドを作成。UIではregionId(int)を送るようにしたい。その場合Daoも要修正
        $this->merge(['regionId' => $this->input('regionIds')[0]]);
        $this->merge($this->SearchableBoundaryValidatorField());
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('dataType'),
            $this->input('productName'),
            $this->input('companyIds'),
            $this->input('regionIds'),
            $this->input('productIds'),
            $this->input('channels'),
            $this->input('cmType'),
            $this->input('cmSeconds'),
            $this->input('progIds')
        );
    }
}
