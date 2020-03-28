<?php

namespace App\Http\UserInterfaces\ProgramList\Get;

use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProgramList\Get\UseCases\InputData;

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
            'wdays' => 'required|array',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['PROGRAM_LIST']),
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
    }

    public function messages()
    {
        return [
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
            'wdays.required' => '曜日の選択は必須です。',
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
        list($isRt, $isTs, $isGross, $isTotal, $isRtTotal) = createDataTypeFlags($this->input('dataType'));
        $dataTypeFlags = ['isRt' => $isRt, 'isTs' => $isTs, 'isGross' => $isGross, 'isTotal' => $isTotal, 'isRtTotal' => $isRtTotal];
        $dataTypeConst = [
            'rt' => \Config::get('const.DATA_TYPE_NUMBER.REALTIME'),
            'ts' => \Config::get('const.DATA_TYPE_NUMBER.TIMESHIFT'),
            'total' => \Config::get('const.DATA_TYPE_NUMBER.TOTAL'),
            'gross' => \Config::get('const.DATA_TYPE_NUMBER.GROSS'),
            'rtTotal' => \Config::get('const.DATA_TYPE_NUMBER.RT_TOTAL'),
        ];
        $prefixes = [
            'code' => \Config::get('const.SAMPLE_CODE_PREFIX'),
            'number' => \Config::get('const.SAMPLE_CODE_NUMBER_PREFIX'),
        ];

        \UserInfo::execute(\Auth::id());

        if (!\Auth::getUser()->isDuringTrial($this->input('startDateTime'), $this->input('endDateTime'))) {
            throw new TrialException(\Auth::getUser());
        }

        $this->inputData = new
        InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('digitalAndBs'),
            $this->input('digitalKanto'),
            $this->input('bs1'),
            $this->input('bs2'),
            $this->input('holiday'),
            $this->input('dataType'),
            $this->input('wdays'),
            $this->input('genres', []),
            $this->input('programNames', []),
            $this->input('order'),
            $this->input('dispCount'),
            $this->input('dateRange'),
            $this->input('page'),
            $this->input('regionId'),
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('csvFlag'),
            $this->input('draw'),
            $this->input('codes'),
            $dataTypeFlags,
            \Auth::id(),
            \Auth::getUser()->hasPermission('smart2::program_extend::view'),
            \Config::get('const.BASE_DIVISION'),
            \Config::get('const.SAMPLE_COUNT_MAX_NUMBER'),
            $dataTypeConst,
            $prefixes,
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME'),
            \Config::get('const.MAX_CODE_NUMBER')
        );
    }
}
