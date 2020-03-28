<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class SearchableBoundaryValidator implements Rule
{
    private $minDate;

    private $maxDate;

    private $isTimeshiftSearchRangeException = false;

    private $isRealtimeSearchRangeException = false;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $st = new Carbon($value['startDateTime']);
        $ed = new Carbon($value['endDateTime']);

        // TODO : takata/将来的にはUI側で全ページ'dataType'を送るようにしたい
        if ($value['dataType'] !== null &&
            (in_array(\Config::get('const.DATA_TYPE_NUMBER.TIMESHIFT'), $value['dataType']) ||
            in_array(\Config::get('const.DATA_TYPE_NUMBER.GROSS'), $value['dataType']) ||
            in_array(\Config::get('const.DATA_TYPE_NUMBER.TOTAL'), $value['dataType']) ||
            in_array(\Config::get('const.DATA_TYPE_NUMBER.RT_TOTAL'), $value['dataType']))
        ) {
            $isInvalid = false;
            $this->maxDate = Carbon::now()->startOfDay()->subDay(\Config::get('const.TS_CALCULATED_DAYS')[$value['regionId']]);

            if ($ed->startOfDay()->greaterThanOrEqualTo($this->maxDate)) {
                $isInvalid = true;
            }
            $this->minDate = new Carbon(\Config::get('const.TS_MIN_FROM_DATE')[$value['regionId']]);

            if ($st->startOfDay()->lessThan($this->minDate)) {
                $isInvalid = true;
            }

            if ($isInvalid) {
                $this->isTimeshiftSearchRangeException = true;
                return false;
            }
        } else {
            $isInvalid = false;
            $this->minDate = new Carbon(\Config::get('const.RT_MIN_FROM_DATE')[$value['regionId']]);

            if ($st->startOfDay()->lessThan($this->minDate)) {
                $isInvalid = true;
            }

            if ($isInvalid) {
                $this->isRealtimeSearchRangeException = true;
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->isTimeshiftSearchRangeException) {
            return '期間は' . $this->minDate->format('Y-m-d') . '～放送日より7日前以上開けて指定してください。※タイムシフト／総合視聴率を含む場合';
        }

        if ($this->isRealtimeSearchRangeException) {
            return '期間は' . $this->minDate->format('Y-m-d') . '以降で指定してください。';
        }

        return '';
    }
}
