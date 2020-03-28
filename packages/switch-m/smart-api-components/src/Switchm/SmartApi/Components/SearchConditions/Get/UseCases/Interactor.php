<?php

namespace Switchm\SmartApi\Components\SearchConditions\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionDao;

/**
 * Class Interactor.
 */
final class Interactor implements InputBoundary
{
    const CONDITION_DATA_TYPE = 'DataType';

    const CONDITION_BS1 = 'Bs1';

    const CONDITION_BS2 = 'Bs2';

    const CONDITION_CM_MATERIALS = 'CmMaterials';

    const CONDITION_CM_TYPE = 'CmType';

    const CONDITION_SAMPLES = 'Samples';

    private $searchConditionDao;

    private $attrDivDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param SearchConditionDao $searchConditionDao
     * @param AttrDivDao $attrDivDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(SearchConditionDao $searchConditionDao, AttrDivDao $attrDivDao, OutputBoundary $outputBoundary)
    {
        $this->searchConditionDao = $searchConditionDao;
        $this->attrDivDao = $attrDivDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $results = $this->searchConditionDao->findByMemberId($inputData->regionId(), $inputData->memberId(), $inputData->orderColumn(), $inputData->orderDirection());

        foreach ($results as $result) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            $condition = json_decode($result->condition, true);
            $content = $condition['conditions'][$condition['conditionKey']];

            // CMランキングオプション
            if (!$inputData->permissionRankingCommercial()) {
                if ($result->route_name === 'main.ranking.commercial') {
                    $result->valid = false;
                    continue;
                }
            }

            // タイムシフトオプション
            if (!$inputData->permissionTimeShifting() && array_key_exists(self::CONDITION_DATA_TYPE, $content)) {
                $dataType = $content[self::CONDITION_DATA_TYPE];

                foreach ($dataType as $value) {
                    if ($value !== 0) {
                        $result->valid = false;
                        continue 2;
                    }
                }
            }

            // BSオプション
            if (!$inputData->permissionBsInfo() && (array_key_exists(self::CONDITION_BS1, $content) || array_key_exists(self::CONDITION_BS2, $content))) {
                $result->valid = false;
                continue;
            }

            // CM素材オプション
            if (!$inputData->permissionCmMaterials() && array_key_exists(self::CONDITION_CM_MATERIALS, $content)) {
                $cmMaterials = $content[self::CONDITION_CM_MATERIALS]['list'];

                if (!empty($cmMaterials)) {
                    $result->valid = false;
                    continue;
                }
            }

            // TIME/SPOTオプション
            if (!$inputData->permissionTimeSpot() && array_key_exists(self::CONDITION_CM_TYPE, $content)) {
                $cmType = $content[self::CONDITION_CM_TYPE];

                if ($cmType !== 0) {
                    $result->valid = false;
                    continue;
                }
            }

            // 掛け合わせオプション
            if (!$inputData->permissionMultipleCondition() && array_key_exists(self::CONDITION_SAMPLES, $content)) {
                $division = $content[self::CONDITION_SAMPLES]['division'];

                if ($division === 'condition_cross') {
                    $result->valid = false;
                    continue;
                }
            }

            // カスタム区分削除対応
            if (array_key_exists(self::CONDITION_SAMPLES, $content)) {
                $samples = $content[self::CONDITION_SAMPLES];

                if (strpos($samples['division'], 'custom') !== false) {
                    foreach ($samples['codes'] as $code) {
                        if ($code['code'] === 'personal' || $code['code'] === 'household') {
                            continue;
                        }
                        $attrDivs = $this->attrDivDao->getAttrDiv($samples['division'], $code['code']);

                        if (empty($attrDivs['list'])) {
                            $result->valid = false;
                            continue 2;
                        }
                    }
                }
            }

            $result->valid = true;
        }

        ($this->outputBoundary)(new OutputData($results));
    }
}
