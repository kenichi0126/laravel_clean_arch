<?php

namespace Switchm\SmartApi\Components\PanelStructure\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\PanelStructureDao;

class Interactor implements InputBoundary
{
    private $panelStructureDao;

    private $memberOriginalDivDao;

    private $outputBoundary;

    /**
     * @param PanelStructureDao $panelStructureDao
     * @param MemberOriginalDivDao $memberOriginalDivDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(
        PanelStructureDao $panelStructureDao,
        MemberOriginalDivDao $memberOriginalDivDao,
        OutputBoundary $outputBoundary
    ) {
        $this->panelStructureDao = $panelStructureDao;
        $this->memberOriginalDivDao = $memberOriginalDivDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        // カスタム区分のdivision一覧
        $customDivisions = [];

        if ($inputData->isBaseFiveDivision()) {
            $memberCustomDivs = $this->memberOriginalDivDao->selectWithMenu($inputData->userId(), 'setting', $inputData->regionId());

            foreach ($memberCustomDivs as $val) {
                array_push($customDivisions, $val->division);
            }
        }

        $attrDivs = [];

        // カスタム区分のattr_divsデータ
        if (!empty($customDivisions)) {
            $customAttrDivs = $this->memberOriginalDivDao->selectCodes($customDivisions);
            $attrDivs['custom'] = $this->convertAttrDivs($customAttrDivs);
        }

        // ベース区分のattr_divsデータ
        $baseAttrDivs = $this->memberOriginalDivDao->selectCodes([$inputData->division()]);
        $attrDivs['base'] = $this->convertAttrDivs($baseAttrDivs);

        $panelData = $this->panelStructureDao->getPanelData($attrDivs, $inputData->regionId());

        $outputData = new OutputData($attrDivs, $panelData, $inputData->isBaseFiveDivision());

        ($this->outputBoundary)($outputData);
    }

    /**
     * @param array $attrDivs
     * @return array
     */
    private function convertAttrDivs(array $attrDivs): array
    {
        $convertedAttrDivs = [];

        foreach ($attrDivs as $key => $value) {
            $definitionText = $this->memberOriginalDivDao->selectDefinitionText(explode(':', $value->definition));

            $appendAttrDiv = [];
            $appendAttrDiv['display_order'] = $key + 1;
            $appendAttrDiv['division'] = $value->division;
            $appendAttrDiv['code'] = $value->code;
            $appendAttrDiv['name'] = $value->name;
            $appendAttrDiv['definition'] = $value->definition;
            $appendAttrDiv['definition_text'] = $definitionText;
            $appendAttrDiv['restore_info'] = $value->restore_info;
            $appendAttrDiv['restore_info_text'] = $value->restore_info_text;
            $convertedAttrDivs[$value->division][$value->code] = $appendAttrDiv;
        }
        return $convertedAttrDivs;
    }
}
