<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerHourly\Get;

use App\Http\UserInterfaces\RatingPerHourly\Get\CsvPresenter;
use ReflectionClass;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputData;
use Tests\TestCase;

class CsvPresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new CsvPresenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     * @dataProvider getFileNameDataProvider
     * @param mixed $channelType
     * @param mixed $displayType
     * @param mixed $aggregateType
     * @param mixed $startDateShort
     * @param mixed $endDateShort
     * @param mixed $expected
     * @throws \ReflectionException
     */
    public function getFileName($channelType, $displayType, $aggregateType, $startDateShort, $endDateShort, $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getFileName');
        $method->setAccessible(true);

        $actual = $method->invoke(
            $this->target,
            $channelType,
            $displayType,
            $aggregateType,
            $startDateShort,
            $endDateShort
        );

        $this->assertSame($expected, $actual);
    }

    public function getFileNameDataProvider()
    {
        return [
            ['summary', '', 'hourly', '20190101', '20190107', 'SMI_TIMEsum_20190101-20190107.csv'],
            ['dt1', 'channelBy', 'daily', '20190101', '20190107', 'SMI_TIMEch_20190101-20190107-mdaily.csv'],
            ['test', 'test', 'other', '20190101', '20190107', 'SMI_TIMEday_20190101-20190107-mother.csv'],
        ];
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $expected = "\n\n";

        ob_start();
        $output = new OutputData(
            ['data' => []],
            1,
            100,
            [],
            'summary',
            '',
            '',
            '20200101',
            '20200107',
            ['header' => []]
        );

        $this->target->__invoke($output);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertSame($expected, $actual);
    }
}
