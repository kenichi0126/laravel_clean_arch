<?php

namespace Switchm\SmartApi\Components\Tests\Common;

use Carbon\Carbon;
use Switchm\SmartApi\Components\Common\DateTimeInputDataTrait;
use Switchm\SmartApi\Components\Tests\TestCase;

class DateTimeInputDataTraitTest extends TestCase
{
    private const START_DATETIME = '2019-11-23 05:00:00';

    private const END_DATETIME = '2019-11-24 04:59:59';

    private const START_SECOUNDS = '00';

    private const END_SECOUNDS = '59';

    private $carbonStartDatetime;

    private $carbonEndDatetime;

    /**
     * @var __anonymous@366
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = new class(self::START_DATETIME, self::END_DATETIME) {
            use DateTimeInputDataTrait;

            public function __construct($s, $e)
            {
                $this->startDateTime = Carbon::parse($s);
                $this->endDateTime = Carbon::parse($e);
            }
        };

        $this->carbonStartDatetime = Carbon::parse(self::START_DATETIME);
        $this->carbonEndDatetime = Carbon::parse(self::END_DATETIME);
    }

    /**
     * @test
     */
    public function carbonStartDateTime(): void
    {
        $expected = $this->carbonStartDatetime->copy();
        $actual = $this->target->carbonStartDateTime();

        // assertEqualsで
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function carbonMinusFiveStartDateTime(): void
    {
        $expected = $this->carbonStartDatetime->subHour(5)->copy();
        $actual = $this->target->carbonMinusFiveStartDateTime();

        // assertEqualsで
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function carbonEndDateTime(): void
    {
        $expected = $this->carbonEndDatetime->copy();
        $actual = $this->target->carbonEndDateTime();

        // assertEqualsで
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function carbonMinusFiveEndDateTime(): void
    {
        $expected = $this->carbonEndDatetime->subHour(5)->copy();
        $actual = $this->target->carbonMinusFiveEndDateTime();

        // assertEqualsで
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function startDateTime(): void
    {
        $expected = $this->carbonStartDatetime->format('Y-m-d H:i:s');
        $actual = $this->target->startDateTime();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startDateTimeShort(): void
    {
        $expected = $this->carbonStartDatetime->format('YmdHis');
        $actual = $this->target->startDateTimeShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startDate(): void
    {
        $expected = $this->carbonStartDatetime->format('Y-m-d');
        $actual = $this->target->startDate();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startDateShort(): void
    {
        $expected = $this->carbonStartDatetime->format('Ymd');
        $actual = $this->target->startDateShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startHour(): void
    {
        $expected = $this->carbonStartDatetime->format('H');
        $actual = $this->target->startHour();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startMinute(): void
    {
        $expected = self::START_SECOUNDS;
        $actual = $this->target->startMinute();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startSecond(): void
    {
        $expected = self::START_SECOUNDS;
        $actual = $this->target->startSecond();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startTime(): void
    {
        $expected = sprintf('%s%s', $this->carbonStartDatetime->format('H:i:'), self::START_SECOUNDS);
        $actual = $this->target->startTime();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function minusFiveStartTime(): void
    {
        $expected = sprintf('%s%s', $this->carbonStartDatetime->subHour(5)->format('H:i:'), self::START_SECOUNDS);
        $actual = $this->target->minusFiveStartTime();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function startTimeShort(): void
    {
        $expected = sprintf('%s%s', $this->carbonStartDatetime->format('Hi'), self::START_SECOUNDS);
        $actual = $this->target->startTimeShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function minusFiveStartTimeShort(): void
    {
        $expected = sprintf('%s%s', $this->carbonStartDatetime->subHour(5)->format('Hi'), self::START_SECOUNDS);
        $actual = $this->target->minusFiveStartTimeShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endDateTime(): void
    {
        $expected = $this->carbonEndDatetime->format('Y-m-d H:i:s');
        $actual = $this->target->endDateTime();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endDateTimeShort(): void
    {
        $expected = $this->carbonEndDatetime->format('YmdHis');
        $actual = $this->target->endDateTimeShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endDate(): void
    {
        $expected = $this->carbonEndDatetime->format('Y-m-d');
        $actual = $this->target->endDate();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endDateShort(): void
    {
        $expected = $this->carbonEndDatetime->format('Ymd');
        $actual = $this->target->endDateShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endHour(): void
    {
        $expected = $this->carbonEndDatetime->format('H');
        $actual = $this->target->endHour();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endMinute(): void
    {
        $expected = self::END_SECOUNDS;
        $actual = $this->target->endMinute();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endSecond(): void
    {
        $expected = self::END_SECOUNDS;
        $actual = $this->target->endSecond();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endTime(): void
    {
        $expected = sprintf('%s%s', $this->carbonEndDatetime->format('H:i:'), self::END_SECOUNDS);
        $actual = $this->target->endTime();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function minusFiveEndTime(): void
    {
        $expected = sprintf('%s%s', $this->carbonEndDatetime->subHour(5)->format('H:i:'), self::END_SECOUNDS);
        $actual = $this->target->minusFiveEndTime();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function endTimeShort(): void
    {
        $expected = sprintf('%s%s', $this->carbonEndDatetime->format('Hi'), self::END_SECOUNDS);
        $actual = $this->target->endTimeShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function minusFiveEndTimeShort(): void
    {
        $expected = sprintf('%s%s', $this->carbonEndDatetime->subHour(5)->format('Hi'), self::END_SECOUNDS);
        $actual = $this->target->minusFiveEndTimeShort();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function searchDays(): void
    {
        $expected = 1;
        $actual = $this->target->searchDays();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function searchHours(): void
    {
        $expected = 24;
        $actual = $this->target->searchHours();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function straddlingFlg(): void
    {
        $expected = true;
        $actual = $this->target->straddlingFlg();

        $this->assertSame($expected, $actual);
    }
}
