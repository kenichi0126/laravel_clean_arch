<?php

namespace Switchm\SmartApi\Components\Divisions\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;

class Interactor implements InputBoundary
{
    private $divisionDao;

    private $memberOriginalDivDao;

    private $outputBoundary;

    /**
     * @param DivisionDao $divisionDao
     * @param MemberOriginalDivDao $memberOriginalDivDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(DivisionDao $divisionDao, MemberOriginalDivDao $memberOriginalDivDao, OutputBoundary $outputBoundary)
    {
        $this->divisionDao = $divisionDao;
        $this->memberOriginalDivDao = $memberOriginalDivDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $divisions = [
            'ga8',
            'ga12',
            'ga10s',
            'gm',
            'oc',
        ];

        // オリジナル属性
        $orgDivisions = [];
        $originalList = [];
        $result = $this->memberOriginalDivDao->selectWithMenu($inputData->userInfo()->id, $inputData->menu(), $inputData->regionId());

        foreach ($result as $key => $val) {
            array_push($orgDivisions, $val->division);
        }

        if (!empty($orgDivisions)) {
            // オリジナル属性の取得
            $originalList = json_decode(json_encode($this->divisionDao->findOriginalDiv($orgDivisions, $inputData->userInfo()->id, $inputData->regionId())), true);
        }
        // 属性の取得
        $basicList = json_decode(json_encode($this->divisionDao->find($divisions)), true);

        $list = array_merge_recursive($basicList, $originalList);

        $divisions = [];
        $divisionMaps = [];
        // division でハッシュマップ化
        foreach ($list as $key => $val) {
            $divisions[$val['division']] = $val;

            if (!array_key_exists($val['division'], $divisionMaps)) {
                $divisionMaps[$val['division']] = [];
            }
            array_push($divisionMaps[$val['division']], $val);
        }

        // 個人、世帯の取得
        $personalHousehold = json_decode(json_encode($this->divisionDao->getPersonalHouseHold()), true);

        // 個人を先頭、世帯を末尾に付与
        foreach ($divisionMaps as $key => $val) {
            array_unshift($divisionMaps[$key], $personalHousehold[0]);
            array_push($divisionMaps[$key], $personalHousehold[1]);
        }

        // 例外判定
        if (count($list) <= 0 || count($personalHousehold) <= 0) {
            // TODO: takata/例外クラスを作成する（DataNotExistException）
            return;
        }

        if ($inputData->hasCrossConditionPermission()) {
            $divisions = array_merge($divisions, [
                'condition_cross' => [
                    'code' => 'condition_cross',
                    'division' => 'condition_cross',
                    'division_name' => '掛け合わせ条件',
                    'name' => '',
                ],
            ]);
        }

        $output = new OutputData($divisions, $divisionMaps);

        ($this->outputBoundary)($output);
    }
}
