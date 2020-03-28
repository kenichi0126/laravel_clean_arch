<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

use Switchm\SmartApi\Queries\Dao\AbstractPdoDao;

class Dao extends AbstractPdoDao
{
    protected $connectionName;

    public function __construct()
    {
        $this->connectionName = 'smart_dwh';
    }

    public function createRtSampleTempTable(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal = false, ?int $codeNumber = null): void
    {
        $timeBoxIdsWhere = '';
        $bindings = [];

        if (count($timeBoxIds) > 0) {
            $bindTimeBoxIds = $this->createArrayBindParam('time_box_ids', ['time_box_ids' => $timeBoxIds], $bindings);
            $timeBoxIdsWhere .= ' tbp.time_box_id IN (' . implode(',', $bindTimeBoxIds) . ') ';
        }

        // codeごとのwhere句
        $caseWhenArr = [];

        if ($isConditionCross) {
            $caseWhenArr = $this->createConditionCrossArray($conditionCross, $bindings);
        } else {
            $caseWhenArr = $this->createCrossJoinArray($division, $codes, $bindings);
        }

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE IF NOT EXISTS samples ( ';
        $sql .= '   time_box_id INT ';
        $sql .= '   , paneler_id INT ';

        for ($i = 0; $i < $codeNumber; $i++) {
            $code = sprintf('%s%02d', $sampleCodePrefix, $i);
            $sql .= " ,${code} SMALLINT ";
        }
        $sql .= ' ) DISTSTYLE ALL SORTKEY (time_box_id); ';
        $this->select($sql);

        $sql = '';
        $sql .= 'SELECT EXISTS(SELECT * FROM samples) as has_record';
        $result = $this->selectOne($sql);

        if (!empty($result->has_record)) {
            return;
        }

        $sql = '';
        $sql .= ' INSERT INTO samples ';
        $sql .= ' SELECT ';
        $sql .= '   tbp.time_box_id ';
        $sql .= '   , tbp.paneler_id ';

        for ($i = 0; $i < $codeNumber; $i++) {
            if (isset($caseWhenArr[$i])) {
                $sql .= ', CASE WHEN ' . $caseWhenArr[$i]['condition'] . ' THEN 1 ELSE 0 END ';
            } else {
                $sql .= ', 0 ';
            }
            $sql .= sprintf(' %s%02d ', $sampleCodePrefix, $i);
        }
        $sql .= ' FROM ';
        $sql .= '   time_box_panelers tbp ';
        $sql .= ' WHERE ' . $timeBoxIdsWhere;

        $this->insertTemporaryTable($sql, $bindings);

        $sql = '';
        $sql .= ' ANALYZE samples; ';
        $this->select($sql);

        $selectedPersonals = [];
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE rt_numbers AS ';
        $sql .= '   SELECT';
        $sql .= '     time_box_id';

        for ($i = 0; $i < $codeNumber; $i++) {
            $code = sprintf('%s%02d', $sampleCodePrefix, $i);
            $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
            $sql .= " , SUM(${code}) ";
            $sql .= " ${number} ";

            if ($hasSelectedPersonal && isset($caseWhenArr[$i])) {
                $selectedPersonals[] = $number;
            }
        }

        if ($hasSelectedPersonal) {
            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodeNumberPrefix);
            $sql .= ' ,' . implode(' + ', $selectedPersonals) . " ${code}";
        }
        $sql .= '   FROM';
        $sql .= '     samples';
        $sql .= '   GROUP BY';
        $sql .= '     time_box_id';
        $this->select($sql);
    }

    public function createTsSampleTempTable(bool $isConditionCross, array $conditionCross, String $division, array $codes, array $timeBoxIds, int $regionId, string $sampleCodePrefix, string $sampleCodeNumberPrefix, string $selectedPersonalName, bool $hasSelectedPersonal = false, ?int $codeNumber = null): void
    {
        $timeBoxIdsWhere = '';
        $bindings = [];

        if (count($timeBoxIds) > 0) {
            $bindTimeBoxIds = $this->createArrayBindParam('time_box_ids', ['time_box_ids' => $timeBoxIds], $bindings);
            $timeBoxIdsWhere .= ' tbp.time_box_id IN (' . implode(',', $bindTimeBoxIds) . ') ';
        }

        // codeごとのwhere句
        $caseWhenArr = [];

        if ($isConditionCross) {
            $caseWhenArr = $this->createConditionCrossArray($conditionCross, $bindings);
        } else {
            $caseWhenArr = $this->createCrossJoinArray($division, $codes, $bindings);
        }

        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE IF NOT EXISTS ts_samples ( ';
        $sql .= '   time_box_id INT ';
        $sql .= '   , paneler_id INT ';

        for ($i = 0; $i < $codeNumber; $i++) {
            $code = sprintf('%s%02d', $sampleCodePrefix, $i);
            $sql .= " ,${code} SMALLINT ";
        }
        $sql .= ' ) DISTSTYLE ALL SORTKEY (time_box_id); ';
        $this->select($sql);

        $sql = '';
        $sql .= 'SELECT EXISTS(SELECT * FROM ts_samples) as has_record';
        $result = $this->selectOne($sql);

        if (!empty($result->has_record)) {
            return;
        }

        $sql = '';
        $sql .= ' INSERT INTO ts_samples ';
        $sql .= ' SELECT ';
        $sql .= '   tbp.time_box_id ';
        $sql .= '   , tbp.paneler_id ';

        for ($i = 0; $i < $codeNumber; $i++) {
            if (isset($caseWhenArr[$i])) {
                $sql .= ', CASE WHEN ' . $caseWhenArr[$i]['condition'] . ' THEN 1 ELSE 0 END ';
            } else {
                $sql .= ', 0 ';
            }
            $sql .= sprintf(' %s%02d ', $sampleCodePrefix, $i);
        }
        $sql .= ' FROM ';
        $sql .= '   ts_time_box_panelers tbp ';
        $sql .= ' WHERE ' . $timeBoxIdsWhere;

        $this->insertTemporaryTable($sql, $bindings);

        $sql = '';
        $sql .= ' ANALYZE ts_samples; ';
        $this->select($sql);

        $selectedPersonalCaseArray = [];
        $sql = '';
        $sql .= ' CREATE TEMPORARY TABLE ts_numbers AS ';
        $sql .= '   SELECT ';
        $sql .= '     time_box_id ';

        for ($i = 0; $i < $codeNumber; $i++) {
            $code = sprintf('%s%02d', $sampleCodePrefix, $i);
            $number = sprintf('%s%02d', $sampleCodeNumberPrefix, $i);
            $sql .= " , SUM(${code}) ";
            $sql .= " ${number} ";

            if ($hasSelectedPersonal && isset($caseWhenArr[$i])) {
                $selectedPersonals[] = $number;
            }
        }

        if ($hasSelectedPersonal) {
            $code = sprintf('%s_%s', $selectedPersonalName, $sampleCodeNumberPrefix);
            $sql .= ' ,' . implode(' + ', $selectedPersonals) . " ${code}";
        }
        $sql .= '   FROM';
        $sql .= '     ts_samples';
        $sql .= '   GROUP BY';
        $sql .= '     time_box_id';
        $this->select($sql);
    }

    protected function insertTemporaryTable(string $query, array $bindings = []): bool
    {
        return $this->getConnection()->insert($query, $bindings);
    }
}
