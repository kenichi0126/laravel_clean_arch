<?php

namespace App\Http\UserInterfaces\RatingPerMinutes\Get;

use Carbon\Carbon;
use function getRdbDwhSearchPeriod;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\InputData;

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
            'division' => 'in:ga8,ga10s,ga12,gm,oc',
        ];
    }

    public function messages()
    {
        return [
            'startDateTime.required' => '検索開始日は必須です。',
            'endDateTime.required' => '検索終了日は必須です。',
            'division.in' => '基本属性サンプルのみ選択可能です。',
        ];
    }

    /**
     * @throws TrialException
     */
    public function passedValidation(): void
    {
        if (!\Auth::getUser()->isDuringTrial($this->input('startDateTime'), $this->input('endDateTime'))) {
            throw new TrialException(\Auth::getUser());
        }

        $rdbDwhSearchPeriod = getRdbDwhSearchPeriod(new Carbon($this->input('startDateTime')), new Carbon($this->input('endDateTime')));

        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('regionId'),
            $this->input('channels'),
            $this->input('channelType'),
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('csvFlag'),
            $this->input('draw'),
            $this->input('code'),
            $this->input('dataDivision'),
            $this->input('dataType'),
            $this->input('displayType'),
            $this->input('aggregateType'),
            $this->input('hour'),
            \Config::get('const.SAMPLE_COUNT_MAX_NUMBER'),
            \Auth::id(),
            $rdbDwhSearchPeriod,
            \Config::get('const.BASE_DIVISION'),
            \Config::get('const.RATING_POINTS_LATEST_DATE_TIME_INTERVAL.PER_HOURLY'),
            \Config::get('const.RATING_POINTS_LATEST_DATE_TIME_INTERVAL.PER_MINUTES')
        );
    }
}
