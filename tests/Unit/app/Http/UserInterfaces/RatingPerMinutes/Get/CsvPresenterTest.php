<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerMinutes\Get;

use App\Http\UserInterfaces\RatingPerMinutes\Get\CsvPresenter;
use ReflectionClass;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputData;
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
     * @param string $channelType
     * @param string $displayType
     * @param string $aggregateType
     * @param string $expected
     * @throws \ReflectionException
     */
    public function getFileName(string $channelType, string $displayType, string $aggregateType, string $expected): void
    {
        $startDateShort = '20190101';
        $endDateShort = '20190107';

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('getFileName');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $channelType, $displayType, $aggregateType, $startDateShort, $endDateShort);

        $this->assertSame($expected, $actual);
    }

    public function getFileNameDataProvider()
    {
        return [
            [
                /*channelType*/ 'test',
                /*displayType*/ 'test',
                /*aggregateType*/'test',
                /*expected*/ 'SMI_TIMEday_20190101-20190107-mtest.csv',
            ],
            [
                /*channelType*/ 'summary',
                /*displayType*/ 'test',
                /*aggregateType*/'hourly',
                /*expected*/ 'SMI_TIMEsum_20190101-20190107.csv',
            ],
            [
                /*channelType*/ 'dt1',
                /*displayType*/ 'channelBy',
                /*aggregateType*/'test',
                /*expected*/ 'SMI_TIMEch_20190101-20190107-mtest.csv',
            ],
        ];
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $expected = "\n";

        $output = new OutputData([[]], [[]], [], [], [], '', '', '', '', '', []);

        $this->target->__invoke($output);

        $actual = ob_get_contents();

        $this->assertSame($expected, $actual);
    }
}
