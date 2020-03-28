<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;

/**
 * Class Interactor.
 */
class Interactor implements InputBoundary
{
    private $memberOriginalDivDao;

    private $outputBoundary;

    /**
     * Interactor constructor.
     * @param MemberOriginalDivDao $memberOriginalDivDao
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(MemberOriginalDivDao $memberOriginalDivDao, OutputBoundary $outputBoundary)
    {
        $this->memberOriginalDivDao = $memberOriginalDivDao;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        $divisionList = $this->memberOriginalDivDao->selectWithMenu($inputData->id(), 'setting', $inputData->regionId());

        // member_original_divs に存在しない場合
        if (empty($divisionList)) {
            $output = new OutputData([]);
            ($this->outputBoundary)($output);
            return;
        }

        $divisions = [];
        $originalDivEditFlag = [];

        foreach ($divisionList as $row) {
            $divisions[] = $row->division;
            $originalDivEditFlag[$row->division] = $row->original_div_edit_flag;
        }

        $originalDivisions = $this->memberOriginalDivDao->selectDivisions($inputData->id(), $divisions, $inputData->regionId());

        // attr_divs にも 各画面設定にもない場合
        if (empty($originalDivisions)) {
            $output = new OutputData([]);
            ($this->outputBoundary)($output);
            return;
        }

        $originalDivisionList = [];

        foreach ($originalDivisions as $row) {
            $originalDivisionList[$row->division] = $row->division;
        }

        $codes = $this->memberOriginalDivDao->selectCodes($originalDivisionList);

        $defs = [];
        $codesHash = [];

        if (!empty($codes)) {
            foreach ($codes as $row) {
                $exploded = explode(':', $row->definition);

                foreach ($exploded as $def) {
                    $defs[$def] = $def;
                }
            }
            $defTexts = $this->memberOriginalDivDao->selectDefinitionText($defs);

            $codes = $this->convertDefinition($codes, $defTexts);

            foreach ($codes as $row) {
                if (empty($codesHash[$row->division])) {
                    $codesHash[$row->division] = [];
                }

                if (!in_array($row, $codesHash[$row->division])) {
                    array_push($codesHash[$row->division], $row);
                }
            }
        }

        $divisionHash = [];

        foreach ($originalDivisions as $row) {
            if (empty($divisionHash[$row->division])) {
                $rowspan = 1;
                $codes = [];

                if (array_key_exists($row->division, $codesHash)) {
                    $rowspan = count($codesHash[$row->division]);
                    $codes = $codesHash[$row->division];
                }

                $divisionHash[$row->division] = [
                    'data' => $row,
                    'menus' => [],
                    'original_div_edit_flag' => $originalDivEditFlag[$row->division],
                    'rowspan' => $rowspan,
                ];
            }
            $row->codes = $codes;
            array_push($divisionHash[$row->division]['menus'], [
                'menu' => $row->menu,
                'period' => $row->period,
            ]);
        }

        // 全メニュー判定
        foreach ($divisionHash as $key => $division) {
            if (count($division['menus']) >= 3) {
                $divisionHash[$key]['menus'] = [
                    [
                        'menu' => '全メニュー',
                        'period' => $division['menus'][0]['period'],
                    ],
                ];
                continue;
            }
        }
        $output = new OutputData($divisionHash);
        ($this->outputBoundary)($output);
    }

    /**
     * @param array $codes
     * @param array $defTexts
     * @return array
     */
    private function convertDefinition(array $codes, array $defTexts): array
    {
        $textHash = [];

        foreach ($defTexts as $row) {
            $text = $row->condition_text;
            $split = explode('=', $row->condition_text);

            if (isset($split[1])) {
                if ($split[0] === '性別' || $split[0] === '職業' || $split[0] === '未既婚') {
                    $prefix = $split[0] . '： ';
                    $text = $prefix . $split[1];
                }
            }
            $textHash[$row->def] = $text;
        }

        foreach ($codes as $row) {
            $split = explode(':', $row->definition);
            $texts = [];

            foreach ($split as $def) {
                $tmpArray = explode('=', $def);

                if ($tmpArray[0] === 'age') {
                    // 年齢に関してはそのまま出力
                    $strAge = '年齢： ';
                    $strAge .= $tmpArray[1] . '才';
                    array_push($texts, $strAge);
                } elseif (strpos($tmpArray[0], 'childage') !== false) {
                    switch ($tmpArray[0]) {
                        case 'childage_f':
                            array_push($texts, '子供性別： 女性');
                            break;
                        case 'childage_m':
                            array_push($texts, '子供性別： 男性');
                            break;
                    }
                    $strChildage = '子供年齢： ' . str_replace('_', '-', $tmpArray[1]) . '才';
                    array_push($texts, $strChildage);
                } elseif ($tmpArray[0] === 'paneler_id') {
                    // パネラーIDについては出力しない
                    continue;
                } else {
                    array_push($texts, $textHash[$def]);
                }
            }
            $row->texts = $texts;
        }

        return $codes;
    }
}
