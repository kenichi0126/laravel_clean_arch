<?php

namespace Switchm\SmartApi\Queries\Dao;

use Switchm\SmartApi\Queries\Dao\Rdb\CalcRatingDao;

abstract class AbstractDao
{
    /**
     * かけ合わせ条件作成SQL.
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param unknown $params
     * @param & $bindings
     * @param mixed $alias
     * @return string
     */
    protected function createConditionCrossSql($params, &$bindings, $alias = 'tbp'): String
    {
        return CalcRatingDao::getInstance()->createConditionCrossSql($params, $bindings, $alias);
    }

    protected function createConditionCrossArray(array $params, array &$bindings, string $alias = 'tbp'): array
    {
        return CalcRatingDao::getInstance()->createConditionCrossArray($params, $bindings, $alias);
    }

    /**
     * 配列から、SQLバインド用のパラメータを作成する.
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $keyName
     *                        キー名
     * @param array $valArr
     *                      値の配列
     * @param array $bindArray
     *                         バインド用の配列（参照渡し）
     * @param array $params
     * @param array& $bindings
     * @return array バインドキー名の配列
     */
    protected function createArrayBindParam(string $keyName, array $params, array &$bindings)
    {
        return CalcRatingDao::getInstance()->createArrayBindParam($keyName, $params, $bindings);
    }

    /**
     * TimeBoxのCASE文作成.
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @param string $target
     * @return string
     */
    protected function createTimeBoxCaseClause(String $startDate, String $endDate, int $regionId, String $target): String
    {
        return CalcRatingDao::getInstance()->createTimeBoxCaseClause($startDate, $endDate, $regionId, $target);
    }

    /**
     * オリジナル、拡張属性の時に使用.
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $division
     * @param array $codes
     * @param array $bidings
     * @param string $alias
     * @param array& $bindings
     * @param ?bool $personalFlag
     * @param ?bool $householdFlag
     * @return string
     */
    protected function createCrossJoinWhereClause(String $division, array $codes, array &$bindings, ?bool $personalFlag = false, ?bool $householdFlag = false)
    {
        return CalcRatingDao::getInstance()->createCrossJoinWhereClause($division, $codes, $bindings, $personalFlag, $householdFlag);
    }

    protected function createCrossJoinArray(String $division, array $codes, array &$bindings): array
    {
        return CalcRatingDao::getInstance()->createCrossJoinArray($division, $codes, $bindings);
    }

    /**
     * オリジナル、拡張属性の時に使用.
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $division
     * @param string $code
     * @param string $definition
     * @param array $bindings
     * @param string $alias
     * @return string
     */
    protected function createConditionOriginalDivSql(String $division, String $code, String $definition, array &$bindings, String $alias = 'tbp')
    {
        return CalcRatingDao::getInstance()->createConditionOriginalDivSql($division, $code, $definition, $bindings, $alias);
    }

    /**
     * RedShiftのSortkey を適応するため、冗長的なWHERE句を生成する
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $division
     * @param string $code
     * @param string $definition
     * @param array $bindings
     * @param string $alias
     * @param string $startDate
     * @param string $endDate
     * @param ?String $cmType
     * @param ?String $cmSeconds
     * @param ?array $progIds
     * @param int $regionId
     * @param ?array $codes
     * @param ?array $conditionCross
     * @param ?array $companyIds
     * @param ?array $productIds
     * @param ?array $cmIds
     * @param array $channels
     * @param bool $straddlingFlg
     * @return string
     */
    protected function createCommercialListWhere(String $startDate, String $endDate, ?String $cmType, ?String $cmSeconds, ?array $progIds, int $regionId, String $division, ?array $codes, ?array $conditionCross, ?array $companyIds, ?array $productIds, ?array $cmIds, array $channels, bool $straddlingFlg)
    {
        return CalcRatingDao::getInstance()->createCommercialListWhere($startDate, $endDate, $cmType, $cmSeconds, $progIds, $regionId, $division, $codes, $conditionCross, $companyIds, $productIds, $cmIds, $channels, $straddlingFlg);
    }

    /**
     * RedShiftのSortkey を適応するため、冗長的なWHERE句を生成する
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $division
     * @param string $code
     * @param string $definition
     * @param array $bindings
     * @param string $alias
     * @param string $startDate
     * @param string $endDate
     * @param array $channels
     * @param ?array $genres
     * @param ?array $progIds
     * @param ?array $conditionCross
     * @param ?array $codes
     * @param int $regionId
     * @param ?bool $bsFlg
     * @return string
     */
    protected function createProgramListWhere(string $startDate, string $endDate, array $channels, ?array $genres, ?array $progIds, string $division, ?array $conditionCross, ?array $codes, int $regionId, ?bool $bsFlg)
    {
        return CalcRatingDao::getInstance()->createProgramListWhere($startDate, $endDate, $channels, $genres, $progIds, $division, $conditionCross, $codes, $regionId, $bsFlg);
    }

    /**
     * RedShiftのSortkey を適応するため、冗長的なWHERE句を生成する
     * Dwh配下からもReadRdb配下からも ReadRdb を参照する仕組みにしたいため、
     * CalcRatingDaoを呼び出す。
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $regionId
     * @return array
     */
    protected function createTimeBoxListWhere(String $startDate, String $endDate, int $regionId): array
    {
        return CalcRatingDao::getInstance()->createTimeBoxListWhere($startDate, $endDate, $regionId);
    }

    protected function getPerHourlyLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes): String
    {
        return CalcRatingDao::getInstance()->getPerHourlyLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
    }

    protected function getPerMinutesLatestDateTime(int $regionId, string $intervalHourly, string $intervalMinutes): String
    {
        return CalcRatingDao::getInstance()->getPerMinutesLatestDateTime($regionId, $intervalHourly, $intervalMinutes);
    }
}
