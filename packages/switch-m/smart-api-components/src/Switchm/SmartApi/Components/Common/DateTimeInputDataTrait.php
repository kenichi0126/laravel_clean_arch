<?php

namespace Switchm\SmartApi\Components\Common;

use Carbon\Carbon;

trait DateTimeInputDataTrait
{
    /**
     * @var Carbon
     */
    protected $startDateTime;

    /**
     * @var Carbon
     */
    protected $endDateTime;

    /**
     * @return Carbon
     */
    public function carbonStartDateTime(): Carbon
    {
        return $this->startDateTime->copy();
    }

    /**
     * @return Carbon
     */
    public function carbonMinusFiveStartDateTime(): Carbon
    {
        return $this->carbonStartDateTime()->subHours(5);
    }

    /**
     * @return Carbon
     */
    public function carbonEndDateTime(): Carbon
    {
        return $this->endDateTime->copy();
    }

    /**
     * @return Carbon
     */
    public function carbonMinusFiveEndDateTime(): Carbon
    {
        return $this->carbonEndDateTime()->subHours(5);
    }

    /**
     * @return string
     */
    public function startDateTime(): string
    {
        return $this->startDateTime->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function startDateTimeShort(): string
    {
        return $this->startDateTime->format('YmdHis');
    }

    /**
     * @return string
     */
    public function startDate(): string
    {
        return $this->startDateTime->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function startDateShort(): string
    {
        return $this->startDateTime->format('Ymd');
    }

    /**
     * @return string
     */
    public function startHour(): string
    {
        return $this->startDateTime->format('H');
    }

    /**
     * @return string
     */
    public function startMinute(): string
    {
        return $this->startDateTime->format('i');
    }

    /**
     * @return string
     */
    public function startSecond(): string
    {
        return '00';
    }

    /**
     * @return string
     */
    public function startTime(): string
    {
        return $this->startDateTime->format('H:i:') . $this->startSecond();
    }

    /**
     * @return string
     */
    public function minusFiveStartTime(): string
    {
        return $this->carbonMinusFiveStartDateTime()->format('H:i:') . $this->startSecond();
    }

    /**
     * @return string
     */
    public function startTimeShort(): string
    {
        return $this->startDateTime->format('Hi') . $this->startSecond();
    }

    /**
     * @return string
     */
    public function minusFiveStartTimeShort(): string
    {
        return $this->carbonMinusFiveStartDateTime()->format('Hi') . $this->startSecond();
    }

    /**
     * @return string
     */
    public function endDateTime(): string
    {
        return $this->endDateTime->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function endDateTimeShort(): string
    {
        return $this->endDateTime->format('YmdHis');
    }

    /**
     * @return string
     */
    public function endDate(): string
    {
        return $this->endDateTime->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function endDateShort(): string
    {
        return $this->endDateTime->format('Ymd');
    }

    /**
     * @return string
     */
    public function endHour(): string
    {
        return $this->endDateTime->format('H');
    }

    /**
     * @return string
     */
    public function endMinute(): string
    {
        return $this->endDateTime->format('i');
    }

    /**
     * @return string
     */
    public function endSecond(): string
    {
        return '59';
    }

    /**
     * @return string
     */
    public function endTime(): string
    {
        return $this->endDateTime->format('H:i:') . $this->endSecond();
    }

    /**
     * @return string
     */
    public function minusFiveEndTime(): string
    {
        return $this->carbonMinusFiveEndDateTime()->format('H:i:') . $this->endSecond();
    }

    /**
     * @return string
     */
    public function endTimeShort(): string
    {
        return $this->endDateTime->format('Hi') . $this->endSecond();
    }

    /**
     * @return string
     */
    public function minusFiveEndTimeShort(): string
    {
        return $this->carbonMinusFiveEndDateTime()->format('Hi') . $this->endSecond();
    }

    /**
     * @return int
     */
    public function searchDays(): int
    {
        return $this->endDateTime->diffInDays($this->startDateTime) + 1;
    }

    /**
     * @return int
     */
    public function searchHours(): int
    {
        return $this->endDateTime->diffInHours($this->startDateTime) + 1;
    }

    /**
     * 0時跨ぎ対応（開始時刻のほうが大きくなる場合）.
     *
     * @return bool
     */
    public function straddlingFlg(): bool
    {
        return $this->carbonStartDateTime()->format('Hi') > $this->carbonEndDateTime()->format('Hi');
    }
}
