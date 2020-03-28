<?php

namespace App\Http\UserInterfaces\SearchConditions\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputData;

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
            'orderColumn' => 'required',
            'orderDirection' => 'required',
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
            'orderColumn.required' => '順序カラム名は必須です。',
            'orderDirection.required' => '順序方向は必須です。',
        ];
    }

    public function passedValidation(): void
    {
        $this->inputData = new InputData(
            $this->input('regionId'),
            \Auth::id(),
            $this->input('orderColumn'),
            $this->input('orderDirection'),
            \Auth::getUser()->hasPermission('smart2::ranking_commercial::view'),
            \Auth::getUser()->hasPermission('smart2::time_shifting::view'),
            \Auth::getUser()->hasPermission('smart2::bs_info::view'),
            \Auth::getUser()->hasPermission('smart2::cm_materials::view'),
            \Auth::getUser()->hasPermission('smart2::time_spot::view'),
            \Auth::getUser()->hasPermission('smart2::multiple_condition::view')
        );
    }
}
