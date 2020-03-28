<?php

namespace App\Http\UserInterfaces\ProgramListAverage\Get;

use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProgramListAverage\Get\UseCases\InputData;

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
            // TODO チェック不要？後で確認
        ];
    }

    public function messages()
    {
        return [
        ];
    }

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

        $this->inputData = new InputData(
            $this->input('averageType'),
            $this->input('codes'),
            $this->input('conditionCross'),
            $this->input('dataType'),
            $this->input('digitalAndBs'),
            $this->input('division'),
            $this->input('progIds'),
            $this->input('regionId'),
            $this->input('timeBoxIds'),
            \Config::get('const.BASE_DIVISION'),
            $dataTypeFlags,
            $dataTypeConst,
            $prefixes,
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME'),
            \Config::get('const.MAX_CODE_NUMBER')
        );
    }
}
