<?php

namespace App\Http\UserInterfaces\ProgramMultiChannelProfile\Get;

use App\Rules\SearchableBoundaryValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\InputData;

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
            'regionId' => 'required',
            'progIds' => 'required',
            'timeBoxIds' => 'required',
            'sampleType' => 'required',
            'conditionCross' => 'required',
            'channelIds' => 'required',
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
        if ($this->input('regionId') === 1 && !\Auth::getUser()->hasPermission('smart2::program_target_index_kanto::view')) {
            abort(404);
        } elseif ($this->input('regionId') === 2 && !\Auth::getUser()->hasPermission('smart2::program_target_index_kansai::view')) {
            abort(404);
        }

        if ($this->input('sampleType') === \Config::get('const.SAMPLE_TYPE_NUMBER.ENQ')) {
            $isEnq = true;
        } else {
            $isEnq = false;
        }

        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('regionId'),
            $this->input('progIds'),
            $this->input('timeBoxIds'),
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('codes'),
            $this->input('channelIds'),
            $this->input('sampleType'),
            $isEnq,
            \Config::get('const.SAMPLE_COUNT_MAX_NUMBER'),
            \Config::get('const.ENQ_PROFILE_SAMPLE_THRESHOLD')
        );
    }
}
