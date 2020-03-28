<?php

namespace App\Http\UserInterfaces\RafChart\Get;

use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\InputData;

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
            'companyIds' => 'required_without:productIds',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['RAF']),
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
            'companyIds.required_without' => '企業名または商品名を選択してください。',
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
            $this->input('dataType'),
            $this->input('dateRange'),
            $this->input('regionId'),
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('csvFlag'),
            $this->input('codes'),
            $this->input('channels'),
            $this->input('axisType'),
            $this->input('channelAxis'),
            $this->input('cmIds'),
            $this->input('cmSeconds'),
            $this->input('cmType'),
            $this->input('codeNames'),
            $this->input('companyIds'),
            $this->input('conv_15_sec_flag'),
            $this->input('period'),
            $this->input('productIds'),
            $this->input('progIds'),
            $this->input('reachAndFrequencyGroupingUnit'),
            $dataTypeFlags,
            \Config::get('const.AXIS_TYPE_NUMBER.PRODUCT'),
            \Config::get('const.CSV_RAF_PRODUCT_AXIS_LIMIT'),
            \Auth::id(),
            \Config::get('const.AXIS_TYPE_NUMBER.COMPANY'),
            \Config::get('const.BASE_DIVISION')
        );
    }
}
