<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputData;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionDao;

/**
 * Class InteractorTest.
 */
final class InteractorTest extends TestCase
{
    private $searchConditionDao;

    private $attrDivDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->searchConditionDao = $this->prophesize(SearchConditionDao::class);
        $this->attrDivDao = $this->prophesize(AttrDivDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->searchConditionDao->reveal(), $this->attrDivDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $permissions
     * @param $routeName
     * @param $condition
     * @param $custom
     * @param $attrDivs
     */
    public function invoke($permissions, $routeName, $condition, $custom, $attrDivs): void
    {
        $input = new InputData(
            1,
            1,
            'name',
            'desc',
            $permissions[0],
            $permissions[1],
            $permissions[2],
            $permissions[3],
            $permissions[4],
            $permissions[5]
        );

        $expected = (object) [
            'id' => 0,
            'regionId' => 1,
            'member_id' => 1,
            'route_name' => $routeName,
            'condition' => $condition,
            'created_at' => '2020-01-10 17:31:45',
            'updated_at' => '2020-01-10 17:31:45',
            'deleted_at' => null,
        ];

        $output = new OutputData([$expected]);

        $this->searchConditionDao->findByMemberId(arg::cetera())->willReturn([$expected])->shouldBeCalled();

        if ($custom) {
            $this->attrDivDao->getAttrDiv(arg::cetera())->willReturn($attrDivs)->shouldBeCalled();
        }

        $this->outputBoundary->__invoke($output)->shouldBeCalled();

        $this->target->__invoke($input);
    }

    public function dataProvider()
    {
        return [
            [
                [true, true, true, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "test": "test"
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [false, true, true, true, true, true],
                'main.ranking.commercial',
                '{
                    "conditions": {
                        "test": {
                            "test": "test"
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, false, true, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "DataType": [0]
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, false, true, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "DataType": [0, 1]
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, false, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "test": "test"
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, false, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "Bs2": []
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, true, false, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "CmMaterials": {
				                "list": [
					                {
                                        "bgm": "",
                                        "memo": "テストメモ",
                                        "cm_id": "1409671",
                                        "talent": "テストタレント",
                                        "setting": "テストセッティング",
                                        "duration": 30,
                                        "notExists": false
					                }
					            ],
					            "selected": [
					                "1409671"
					            ]
					        }
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, true, true, false, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "CmType": 0
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, true, true, false, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "CmType": 1
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, true, true, true, false],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "Samples": {
                                "codes": [
                                    {
                                        "code": "personal",
                                        "name": "個人",
                                        "division": "personal",
                                        "division_name": "個人"
                                    },
                                    {
                                        "code": "household",
                                        "name": "世帯",
                                        "division": "household",
                                        "division_name": "世帯"
                                    }
                                ],
                                "division": "ga12"
                            }
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, true, true, true, false],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "Samples": {
                                "codes": [
                                    {
                                        "code": "personal",
                                        "name": "個人",
                                        "division": "personal",
                                        "division_name": "個人"
                                    },
                                    {
                                        "code": "household",
                                        "name": "世帯",
                                        "division": "household",
                                        "division_name": "世帯"
                                    }
                                ],
                                "division": "condition_cross"
                            }
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
            [
                [true, true, true, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "Samples": {
                                "codes": [
                                    {
                                        "code": "201912181901199032311",
                                        "name": "男性",
                                        "division": "custom2311_2019090513551_3",
                                        "definition": "gender=m:age=4-99",
                                        "display_order": 1,
                                        "division_name": "カスタム3",
                                        "division_order": 1
                                    }
                                ],
                                "division": "custom2311_2019090513551_3"
                            }
                        }
                    },
                    "conditionKey": "test"
                }',
                true,
                [
                    'list' => [
                        (object) [
                            'code' => 201912181901199032311,
                            'name' => '男性',
                            'division' => 'custom2311_2019090513551_3',
                            'definition' => 'gender=m:age=4-99',
                            'display_order' => 1,
                            'division_name' => 'カスタム3',
                            'division_order' => 1,
                        ],
                    ],
                ],
            ],
            [
                [true, true, true, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "Samples": {
                                "codes": [
                                    {
                                        "code": "201912181901199032311",
                                        "name": "男性",
                                        "division": "custom2311_2019090513551_3",
                                        "definition": "gender=m:age=4-99",
                                        "display_order": 1,
                                        "division_name": "カスタム3",
                                        "division_order": 1
                                    }
                                ],
                                "division": "custom2311_2019090513551_3"
                            }
                        }
                    },
                    "conditionKey": "test"
                }',
                true,
                [
                    'list' => [],
                ],
            ],
            [
                [true, true, true, true, true, true],
                'test',
                '{
                    "conditions": {
                        "test": {
                            "Samples": {
                                "codes": [
                                    {
                                        "code": "personal",
                                        "name": "個人",
                                        "division": "personal",
                                        "division_name": "個人"
                                    }
                                ],
                                "division": "custom2311_2019090513551_3"
                            }
                        }
                    },
                    "conditionKey": "test"
                }',
                false,
                [],
            ],
        ];
    }
}
