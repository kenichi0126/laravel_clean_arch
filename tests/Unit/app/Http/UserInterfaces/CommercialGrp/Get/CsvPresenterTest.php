<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialGrp\Get;

use App\Http\UserInterfaces\CommercialGrp\Get\CsvPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputData;
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
     * @dataProvider invokeDataProvider
     * @param mixed $list
     * @param mixed $expected
     */
    public function invoke($list, $expected): void
    {
        $output = new OutputData(
            $list,
            1,
            'ga8',
            ['personal'],
            [],
            'period',
            [0],
            '20200101',
            '20200107',
            ['header' => []]
        );

        ob_start();
        $this->target->__invoke($output);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected, $actual);
    }

    public function invokeDataProvider()
    {
        return [
            [[], "\n"],
            [[(object) [
                'rowcount' => 2,
                'date' => '期間計',
                'name' => 'test',
                'product_name' => 'test',
                'total_cnt' => 10,
                'total_duration' => 'test',
                'rt_personal_viewing_grp' => 1,
                'display_name' => 'test',
            ]],
            "\n期間計,test,\"test 計\",10,0,1\n,＜局別内訳＞,test,10,test,1\n,test,企業合計,10,0,1\n",
            ],
        ];
    }
}
