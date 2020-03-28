<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class EnqDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(EnqDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getCategory(): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn([])
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn([])
            ->once();

        $expected = [
            'largeCategories' => [
                ['q_group' => '全選択'],
            ],
            'middleCategories' => [
                ['q_group' => '', 'tag' => '全選択'],
            ],
        ];

        $actual = $this->target->getCategory();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider getQuestionProvider
     * @param $keyword
     * @param $qGroup
     * @param $tag
     * @param $questionExpected
     * @param $resultExpected
     */
    public function getQuestion($keyword, $qGroup, $tag, $questionExpected, $resultExpected): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($questionExpected)
            ->once();

        $actual = $this->target->getQuestion($keyword, $qGroup, $tag);

        $this->assertEquals($resultExpected, $actual);
    }

    public function getQuestionProvider()
    {
        return [
            [
                '全選択',
                '全選択',
                '',
                [
                    (object) [
                        'id' => 3314,
                        'q_no' => 'i3',
                        'item' => 'i3_1',
                        'label' => '',
                        'answer_column' => 'i0003',
                        'q_type' => 'MTS',
                        'a_type' => 'SA',
                        'category_no' => 5,
                        'column_position' => 13,
                        'question' => '自宅のリフォーム・検討意向',
                        'option_no' => 1,
                        'option' => '1年以内に検討したい',
                        'filter' => '18歳以上',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'tag' => 'Q3 自宅のリフォーム／一戸建て住宅の購入／分譲マンションの購入検討意向',
                        'genre_id' => '',
                    ],
                    (object) [
                        'id' => 3315,
                        'q_no' => 'i3',
                        'item' => 'i3_1',
                        'label' => '',
                        'answer_column' => 'i0003',
                        'q_type' => 'MTS',
                        'a_type' => 'SA',
                        'category_no' => 5,
                        'column_position' => 13,
                        'question' => '自宅のリフォーム・検討意向',
                        'option_no' => 2,
                        'option' => '2年以内に検討したい',
                        'filter' => '18歳以上',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'tag' => 'Q3 自宅のリフォーム／一戸建て住宅の購入／分譲マンションの購入検討意向',
                        'genre_id' => '',
                    ],
                ],
                [
                    [
                        'q_no' => 'i3_1',
                        'q_type' => 'MTS',
                        'a_type' => 'SA',
                        'question' => '自宅のリフォーム・検討意向',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'options' => [
                            [
                                'name' => '1年以内に検討したい',
                                'val' => '1',
                                'answer_column' => 'i0003',
                                'q_type' => 'MTS',
                                'a_type' => 'SA',
                                'index' => 1,
                            ],
                            [
                                'name' => '2年以内に検討したい',
                                'val' => '2',
                                'answer_column' => 'i0003',
                                'q_type' => 'MTS',
                                'a_type' => 'SA',
                                'index' => 2,
                            ],
                        ],
                        'tag' => 'Q3 自宅のリフォーム／一戸建て住宅の購入／分譲マンションの購入検討意向',
                    ],
                ],
            ],
            [
                '全選択',
                '全選択',
                '',
                [
                    (object) [
                        'id' => 3301,
                        'q_no' => 'i1',
                        'item' => '',
                        'label' => '',
                        'answer_column' => 'i0001',
                        'q_type' => 'SAR',
                        'a_type' => 'SA',
                        'category_no' => 5,
                        'column_position' => 11,
                        'question' => '家族構成',
                        'option_no' => 1,
                        'option' => '単身世帯',
                        'filter' => '',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'tag' => 'Q1 家族構成',
                        'genre_id' => '',
                    ],
                    (object) [
                        'id' => 3302,
                        'q_no' => 'i1',
                        'item' => '',
                        'label' => '',
                        'answer_column' => 'i0001',
                        'q_type' => 'SAR',
                        'a_type' => 'SA',
                        'category_no' => 5,
                        'column_position' => 11,
                        'question' => '家族構成',
                        'option_no' => 2,
                        'option' => '夫婦のみの世帯',
                        'filter' => '',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'tag' => 'Q1 家族構成',
                        'genre_id' => '',
                    ],
                ],
                [
                    [
                        'q_no' => 'i1',
                        'q_type' => 'SAR',
                        'a_type' => 'SA',
                        'question' => '家族構成',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'options' => [
                            [
                                'name' => '単身世帯',
                                'val' => '1',
                                'answer_column' => 'i0001',
                                'q_type' => 'SAR',
                                'a_type' => 'SA',
                                'index' => 1,
                            ],
                            [
                                'name' => '夫婦のみの世帯',
                                'val' => '2',
                                'answer_column' => 'i0001',
                                'q_type' => 'SAR',
                                'a_type' => 'SA',
                                'index' => 2,
                            ],
                        ],
                        'tag' => 'Q1 家族構成',
                    ],
                ],
            ],
            [
                'a',
                'a',
                'a',
                [
                    (object) [
                        'id' => 3329,
                        'q_no' => 'i4',
                        'item' => 'i4_1',
                        'label' => 'i4_1_1',
                        'answer_column' => 'i0006',
                        'q_type' => 'MTM',
                        'a_type' => 'MA',
                        'category_no' => 12,
                        'column_position' => 16,
                        'question' => '現在の住まいに備わっている設備',
                        'option_no' => 1,
                        'option' => 'ホームセキュリティ',
                        'filter' => '',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'tag' => 'Q4 現在の住まいに備わっている設備／今後、住まいに欲しい設備',
                        'genre_id' => '',
                    ],
                    (object) [
                        'id' => 3330,
                        'q_no' => 'i4',
                        'item' => 'i4_1',
                        'label' => 'i4_1_2',
                        'answer_column' => 'i0007',
                        'q_type' => 'MTM',
                        'a_type' => 'MA',
                        'category_no' => 12,
                        'column_position' => 17,
                        'question' => '現在の住まいに備わっている設備',
                        'option_no' => 2,
                        'option' => 'HEMS（ホームエネルギー管理システム）',
                        'filter' => '',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'tag' => 'Q4 現在の住まいに備わっている設備／今後、住まいに欲しい設備',
                        'genre_id' => '',
                    ],
                ],
                [
                    [
                        'q_no' => 'i4_1',
                        'q_type' => 'MTM',
                        'a_type' => 'MA',
                        'question' => '現在の住まいに備わっている設備 [複数回答可]',
                        'q_group' => '1 家庭について（住まい、自家用車、ペット等）',
                        'options' => [
                            [
                                'name' => 'ホームセキュリティ',
                                'val' => 'i0006',
                                'answer_column' => 'i0006',
                                'q_type' => 'MTM',
                                'a_type' => 'MA',
                                'index' => 1,
                            ],
                            [
                                'name' => 'HEMS（ホームエネルギー管理システム）',
                                'val' => 'i0007',
                                'answer_column' => 'i0007',
                                'q_type' => 'MTM',
                                'a_type' => 'MA',
                                'index' => 2,
                            ],
                        ],
                        'tag' => 'Q4 現在の住まいに備わっている設備／今後、住まいに欲しい設備',
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getSampleCountProvider
     * @param $info
     * @param $conditionCross
     * @param $regionId
     */
    public function getSampleCount($info, $conditionCross, $regionId): void
    {
        $expected = [
            'cnt' => 0,
        ];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionCrossSql')
            ->andReturn('');

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn(
                (object) ['cnt' => 0]
            )
            ->once();

        $actual = $this->target->getSampleCount($info, $conditionCross, $regionId);

        $this->assertEquals($expected, $actual);
    }

    public function getSampleCountProvider()
    {
        return [
            [
                [],
                [],
                1,
            ],
            [
                [
                    [
                        'values' => [
                            [
                                'val' => '1',
                                'answer_column' => 'i0001',
                            ],
                        ],
                        'innerLinkingType' => 'AND',
                        'connectorLinkingType' => 'AND',
                    ],
                    [
                        'values' => [
                            [
                                'val' => '2',
                                'answer_column' => 'i0001',
                            ],
                        ],
                        'innerLinkingType' => 'OR',
                        'connectorLinkingType' => 'OR',
                    ],
                    [
                        'values' => [
                            [
                                'val' => '3',
                                'answer_column' => 'i0001',
                            ],
                        ],
                        'innerLinkingType' => 'AND',
                        'connectorLinkingType' => '',
                    ],
                ],
                [],
                2,
            ],
        ];
    }

    /**
     * @test
     */
    public function getPanelerIds(): void
    {
        $info = [
            [
                'values' => [
                    [
                        'val' => '1',
                        'answer_column' => 'i0001',
                    ],
                ],
                'innerLinkingType' => 'AND',
                'connectorLinkingType' => 'AND',
            ],
            [
                'values' => [
                    [
                        'val' => '2',
                        'answer_column' => 'i0001',
                    ],
                ],
                'innerLinkingType' => 'OR',
                'connectorLinkingType' => 'OR',
            ],
            [
                'values' => [
                    [
                        'val' => '3',
                        'answer_column' => 'i0001',
                    ],
                ],
                'innerLinkingType' => 'AND',
                'connectorLinkingType' => '',
            ],
        ];
        $regionId = 1;

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn([])
            ->once();

        $expected = [
            'list' => [],
        ];

        $actual = $this->target->getPanelerIds($info, $regionId);

        $this->assertEquals($expected, $actual);
    }
}
