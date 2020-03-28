<?php

namespace Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases;

use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;

/**
 * Class Interactor.
 */
class Interactor implements InputBoundary
{
    private $attrDivDao;

    private $enqDao;

    private $outputBoundary;

    private $dataAccess;

    /**
     * Interactor constructor.
     * @param AttrDivDao $attrDivDao
     * @param EnqDao $enqDao
     * @param DataAccessInterface $dataAccess
     * @param OutputBoundary $outputBoundary
     */
    public function __construct(AttrDivDao $attrDivDao, EnqDao $enqDao, DataAccessInterface $dataAccess, OutputBoundary $outputBoundary)
    {
        $this->attrDivDao = $attrDivDao;
        $this->enqDao = $enqDao;
        $this->dataAccess = $dataAccess;
        $this->outputBoundary = $outputBoundary;
    }

    /**
     * @param InputData $inputData
     */
    public function __invoke(InputData $inputData): void
    {
        // attr_div取得
        $attrDivResult = $this->attrDivDao->getAttrDiv($inputData->division(), $inputData->code());

        // 取得できない場合はエラー
        if (count($attrDivResult) < 1) {
            $output = new OutputData(0);
            ($this->outputBoundary)($output);
            return;
        }

        $attrDiv = $attrDivResult['list'][0];

        // code
        $code = $attrDiv->code;

        // 並び順
        $displayOrder = $attrDiv->display_order;

        // 掛け合わせ条件
        $definition = $this->createDefinition($inputData->conditionCross(), $inputData->info(), $inputData->regionId());

        // 質問
        $restoreInfo = $this->createRestoreInfo($inputData->info());

        $result = ($this->dataAccess)(
            $inputData->division(),
            $inputData->code(),
            [
                'division' => $inputData->division(),
                'code' => $code,
                'name' => $inputData->sumpleName(),
                'display_order' => $displayOrder,
                'definition' => $definition,
                'restore_info' => $restoreInfo['info'],
                'restore_info_text' => $restoreInfo['text'],
            ]
        );
        $output = new OutputData($result);
        ($this->outputBoundary)($output);
    }

    // TODO: takata/DRYの原則に反しているので、あとでリファクタリング

    /**
     * attr_div登録・更新用：掛け合わせ条件作成.
     * @param array $conditionCross
     * @param null|array $info
     * @param string $regionId
     * @return string
     */
    private function createDefinition(array $conditionCross, ?array $info, String $regionId): String
    {
        // 掛け合わせ条件を構築する
        $definition = '';

        // 性別
        $gender = $conditionCross['gender'];

        if (isset($gender[0])) {
            $definition .= ':gender=';

            foreach ($gender as $val) {
                $definition .= $val . ',';
            }
            $definition = rtrim($definition, ',');
        }

        // 年齢
        $age = $conditionCross['age'];

        if (isset($age['from'], $age['to'])) {
            $definition .= ":age={$age['from']}-{$age['to']}";
        } else {
            // どちらかが指定されていない場合はデフォルト値を適用する
            $definition .= ':age=4-99';
        }

        // 職業
        $occupation = $conditionCross['occupation'];

        if (isset($occupation[0])) {
            $definition .= ':occupation=';

            foreach ($occupation as $val) {
                $definition .= $val . ',';
            }
            $definition = rtrim($definition, ',');
        }

        // 未既婚
        $married = $conditionCross['married'];

        if (isset($married[0])) {
            $definition .= ':married=';

            foreach ($married as $val) {
                $definition .= $val . ',';
            }
            $definition = rtrim($definition, ',');
        }

        // 子供条件
        $child = $conditionCross['child'];

        if (isset($child) && $child['enabled']) {
            $childAgeFrom = $child['age']['from'];
            $childAgeTo = $child['age']['to'];

            if (isset($childAgeFrom, $childAgeTo)) {
                $key = ':childage';
                $value = "${childAgeFrom}_${childAgeTo}";
                $childGender = $child['gender'];

                if (!empty($childGender) && !empty($childGender[0]) && count($childGender) === 1) {
                    // 性別が指定されている場合
                    if ($childGender[0] === 'f') {
                        $key .= '_f';
                    } elseif ($childGender[0] === 'm') {
                        $key .= '_m';
                    }
                }
                $definition .= $key . '=' . $value;
            }
        }

        // 一番左のコロンを削除
        $definition = ltrim($definition, ':');

        if (!empty($info)) {
            $panelerIds = $this->enqDao->getPanelerIds($info, $regionId);

            $definition .= ':paneler_id=';

            foreach ($panelerIds['list'] as $object) {
                $definition .= $object->paneler_id . ',';
            }
            $definition = rtrim($definition, ',');
        }

        return $definition;
    }

    /**
     * attr_div登録・更新用：質問作成.
     * @param array $info
     * @return array
     */
    private function createRestoreInfo(array $info): array
    {
        $restoreInfo = '';
        $restoreInfoText = '';

        if (!empty($info)) {
            foreach ($info as $groupId => $group) {
                $enq = [];

                foreach ($group['values'] as $key => $value) {
                    $enq[$value['key']]['options'][] = $value;
                    $enq[$value['key']]['q_no'] = $value['q_no'];
                }
                $num = 0;
                $tmpRestoreInfo = '';
                $tmpRestoreInfoText = '';

                foreach ($enq as $key => $value) {
                    $num++;

                    $tmpRestoreInfo .= $value['q_no'] . '=';
                    $tmpRestoreInfoText .= '条件 ' . ($num) . '： ';
                    $tmpRestoreInfoText .= "[{$value['q_no']}]" . $key . '・・・';

                    $restoreInfoArray = [];
                    $restoreInfoTextArray = [];

                    foreach ($value['options'] as $aArray) {
                        $restoreInfoArray[] = $aArray['val'];

                        $restoreInfoTextArray[] = $aArray['index'] . '.' . $aArray['name'];
                    }
                    $tmpRestoreInfo .= implode(',', $restoreInfoArray);
                    $tmpRestoreInfo .= ':';

                    $tmpRestoreInfoText .= implode('、', $restoreInfoTextArray);
                    $tmpRestoreInfoText .= "\n";
                }
                $tmpRestoreInfo .= 'connection_division=' . mb_strtolower($group['innerLinkingType']);
                $tmpRestoreInfoText .= '設問間の条件： ' . $group['innerLinkingType'] . "\n";

                if (count($info) > 1) {
                    $tmpRestoreInfoText = 'グループ ' . ($groupId + 1) . "： {\n${tmpRestoreInfoText} }";
                    $tmpRestoreInfo .= ':group_id=' . $groupId;

                    if (!empty($group['connectorLinkingType'])) {
                        $tmpRestoreInfo .= ':group_connection_division=' . mb_strtolower($group['connectorLinkingType']);
                    }
                }

                $restoreInfo .= $tmpRestoreInfo;
                $restoreInfoText .= $tmpRestoreInfoText;

                if ((count($info) - 1) > $groupId) {
                    $restoreInfoText .= "\nグループ間の条件： " . $group['connectorLinkingType'] . "\n";
                    $restoreInfo .= '|';
                }
            }
        }

        return [
            'info' => $restoreInfo,
            'text' => $restoreInfoText,
        ];
    }
}
