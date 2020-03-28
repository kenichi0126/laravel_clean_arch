<?php

namespace App\Http\UserInterfaces\RankingCommercial\Get;

use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\InputData;

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
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator(['RANKING_CM']),
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
        \UserInfo::execute(\Auth::id());

        if (!\Auth::getUser()->isDuringTrial($this->input('startDateTime'), $this->input('endDateTime'))) {
            throw new TrialException(\Auth::getUser());
        }

        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('page', 0),
            $this->input('holiday'),
            $this->input('wdays', []),
            $this->input('division'),
            $this->input('dateRange'),
            $this->input('dataType'),
            $this->input('regionId'),
            $this->input('cmType'),
            $this->input('codes'),
            $this->input('conditionCross'),
            $this->input('channels'),
            $this->input('order'),
            $this->input('conv_15_sec_flag'),
            $this->input('period'),
            $this->input('dispCount'),
            $this->input('csvFlag'),
            $this->input('cmLargeGenres', []),
            $this->input('axisType'),
            $this->input('draw'),
            \Auth::id(),
            \Config::get('const.BROADCASTER_COMPANY_IDS'),
            \Config::get('const.AXIS_TYPE_NUMBER.COMPANY'),
            \Config::get('const.AXIS_TYPE_NUMBER.PRODUCT'),
            \Config::get('const.BASE_DIVISION')
        );
    }
}
