<?php

namespace App\Http\UserInterfaces\CommercialAdvertising\Get;

use App\Rules\SearchableBoundaryValidator;
use App\Rules\SearchableNumberOfDaysValidator;
use Carbon\Carbon;
use Switchm\Php\Illuminate\Foundation\Http\FormRequest;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\InputData;
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
        $configNames = ['ADVERTISING'];

        if (
            // TODO - fujisaki: UIから送られるデータの型を統一する、もしくはcsvも含めて全てpostで送りboolはboolのまま送るようにする必要がある（対応タイミングは未定）
            $this->input('heatMapRating') === true ||
            $this->input('heatMapRating') === 'true' ||
            $this->input('heatMapTciPersonal') === true ||
            $this->input('heatMapTciPersonal') === 'true' ||
            $this->input('heatMapTciHousehold') === true ||
            $this->input('heatMapTciHousehold') === 'true'
        ) {
            $configNames = array_merge($configNames, ['RATING_POINTS']);
        }

        return [
            'startDateTime' => 'required|date',
            'endDateTime' => 'required|date',
            'companyIds' => 'required_without:productIds|array',
            'SearchableNumberOfDaysValidator' => new SearchableNumberOfDaysValidator($configNames),
            'searchableBoundaryValidator' => new SearchableBoundaryValidator(),
        ];
    }

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

        $rdbDwhSearchPeriod = getRdbDwhSearchPeriod(new Carbon($this->input('startDateTime')), new Carbon($this->input('endDateTime')));

        $this->inputData = new InputData(
            $this->input('startDateTime'),
            $this->input('endDateTime'),
            $this->input('companyIds', []),
            $this->input('productIds', []),
            $this->input('cmType'),
            $this->input('cmSeconds'),
            $this->input('progIds'),
            $this->input('regionId'),
            $this->input('cmIds'),
            $this->input('channels'),
            $this->input('heatMapRating'),
            $this->input('heatMapTciPersonal'),
            $this->input('heatMapTciHousehold'),
            $this->input('division'),
            $this->input('conditionCross'),
            $this->input('csvFlag'),
            $this->input('draw'),
            $this->input('codes'),
            \Auth::id(),
            \Config::get('const.SAMPLE_COUNT_MAX_NUMBER'),
            $rdbDwhSearchPeriod,
            \Config::get('const.BASE_DIVISION'),
            \Config::get('const.RATING_POINTS_LATEST_DATE_TIME_INTERVAL.PER_HOURLY'),
            \Config::get('const.RATING_POINTS_LATEST_DATE_TIME_INTERVAL.PER_MINUTES'),
            \Config::get('const.SAMPLE_CODE_PREFIX'),
            \Config::get('const.SAMPLE_CODE_NUMBER_PREFIX'),
            \Config::get('const.SAMPLE_SELECTED_PERSONAL_NAME')
        );
    }
}
