<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialGrp\Get;

use App\Http\UserInterfaces\CommercialGrp\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialGrp\Get\UseCases\OutputData;
use Tests\TestCase;

class ListPresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new ListPresenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     * @dataProvider invokeDataProvider
     * @param mixed $list
     * @param mixed $count
     * @param mixed $expected
     */
    public function invoke($list, $count, $expected): void
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
            ['header']
        );

        $this->presenterOutput
            ->set([
                'data' => $expected,
                'draw' => $output->draw(),
                'recordsFiltered' => $count,
                'recordsTotal' => $count,
                // TODO: takata/maribelleで対応したらコメント解除する
                // 'header' => $outputData->header()
            ])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }

    public function invokeDataProvider()
    {
        return [
            [[], 0, []],
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
                2,
                [
                    [
                        'date' => '期間計',
                        'company_name' => 'test',
                        'product_name' => 'test 計',
                        'total_count' => 10,
                        'total_duration' => 0,
                        'rt_personal_viewing_grp' => 1, ],
                    [
                        'company_name' => '＜局別内訳＞',
                        'date' => '',
                        'product_name' => 'test',
                        'total_count' => 10,
                        'total_duration' => 'test',
                        'rt_personal_viewing_grp' => 1,
                    ],
                    [
                        'company_name' => 'test',
                        'date' => '',
                        'product_name' => '企業合計',
                        'total_count' => 10,
                        'total_duration' => 0,
                        'rt_personal_viewing_grp' => 1,
                    ],
                ],
            ],
        ];
    }
}
