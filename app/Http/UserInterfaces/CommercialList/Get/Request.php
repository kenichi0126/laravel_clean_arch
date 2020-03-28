<?php

namespace App\Http\UserInterfaces\CommercialList\Get;

use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;

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
            'companyIds' => 'required_without_all:progIds,productIds',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['CMLIST']),
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
    }

    public function messages()
    {
        return [
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
            'companyIds.required_without_all' => '企業名、商品名または番組名を選択してください。',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge($this->SearchableNumberOfDaysValidatorField());
        $this->merge($this->SearchableBoundaryValidatorField());
    }

    /**
     * @throws TrialException
     */
    public function passedValidation(): void
    {
        \UserInfo::execute(\Auth::id());

        if (!\Auth::getUser()->isDuringTrial($this->input('startDateTime'), $this->input('endDateTime'))) {
            throw new TrialException(\Auth::getUser());
        }

        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($this->input('dataType'));
        $dataTypeFlags = ['isRt' => $isRt, 'isTs' => $isTs, 'isGross' => $isGross, 'isTotal' => $isTotal, 'isRtTotal' => $isRtTotal];

        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('page', 0),
            $this->input('dateRange'),
            $this->input('cmType'),
            $this->input('cmSeconds'),
            $this->input('progIds'),
            $this->input('regionId'),
            $this->input('division'),
            $this->input('codes'),
            $this->input('conditionCross'),
            $this->input('companyIds', []),
            $this->input('productIds', []),
            $this->input('cmIds'),
            $this->input('channels'),
            $this->input('order'),
            $this->input('dispCount'),
            $this->input('conv_15_sec_flag'),
            $this->input('csvFlag'),
            $this->input('dataType'),
            $this->input('draw'),
            \Auth::getUser(),
            \Auth::id(),
            \Config::get('const.SAMPLE_COUNT_MAX_NUMBER'),
            $dataTypeFlags,
            \Config::get('const.BASE_DIVISION'),
            \Config::get('const.MAX_CODE_NUMBER'),
            \Config::get('const.SAMPLE_CODE_PREFIX'),
            \Config::get('const.SAMPLE_CODE_NUMBER_PREFIX'),
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME'),
            \Config::get('const.DATA_TYPE_NUMBER'),
            \Auth::getUser()->hasPermission('smart2::cm_materials::view'),
            \Auth::getUser()->hasPermission('smart2::time_spot::view')
        );
    }
}
