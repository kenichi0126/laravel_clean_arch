<?php

namespace Switchm\SmartApi\Queries\Dao\Dwh;

use Carbon\Carbon;

class TopDao extends Dao
{
    public function findProgramRanking(Carbon $from, Carbon $to, array $channelIds): array
    {
        $bindings = [];
        $bindings[':from'] = $from->toDateTimeString();
        $bindings[':to'] = $to->toDateTimeString();
        $bindArr = $this->createArrayBindParam('channelIds', ['channelIds' => $channelIds], $bindings);

        $query = '';
        $query .= ' SELECT ';
        $query .= '   title, ';
        $query .= '   date, ';
        $query .= '   c.code_name, ';
        $query .= '   COALESCE(round(household_viewing_rate,1), 0) personal_viewing_rate ';
        $query .= '   ,COALESCE(round(ts_household_total_viewing_rate,1), 0) ts_personal_viewing_rate ';
        $query .= '   ,COALESCE(round(ts_household_gross_viewing_rate,1), 0) total_viewing_rate ';
        $query .= ' FROM ';
        $query .= '   programs p ';
        $query .= ' INNER JOIN  ';
        $query .= '   channels c ';
        $query .= ' ON ';
        $query .= '   c.id = p.channel_id ';
        $query .= ' WHERE ';
        $query .= '   p.real_started_at BETWEEN :from AND :to ';
        $query .= '   AND p.channel_id IN (' . implode(',', $bindArr) . ') ';
        $query .= '   AND EXTRACT(EPOCH FROM ended_at - started_at) >= 15 * 60 ';
        $query .= ' ORDER BY ';
        $query .= '   household_viewing_rate DESC NULLS LAST ';
        $query .= ' LIMIT 10; ';

        $result = $this->select($query, $bindings);

        return json_decode(json_encode($result), true);
    }

    public function findCmRankingOfCompany(Carbon $from, Carbon $to, int $regionId, array $channelIds, int $conv15SecFlag, array $broadcasterCompanyIds)
    {
        $bindings = [];
        $bindings[':from'] = $from->toDateTimeString();
        $bindings[':to'] = $to->toDateTimeString();
        $bindings[':regionId'] = $regionId;
        $cannnelsBindArr = $this->createArrayBindParam('channelIds', ['channelIds' => $channelIds], $bindings);
        $exclusionBindArr = $this->createArrayBindParam('exclusionCompanyIds', ['exclusionCompanyIds' => $broadcasterCompanyIds], $bindings);

        $query = '';
        $query .= ' SELECT  ';
        $query .= '   c.name company_name ';
        $query .= ' , SUM(round(cm.household_viewing_rate * (cm.duration::real / 15 ), 1))::real grp ';
        $query .= ' , count(*) total_count ';
        $query .= ' FROM ';
        $query .= '   commercials cm ';
        $query .= ' INNER JOIN ';
        $query .= '   companies c ';
        $query .= ' ON ';
        $query .= '   cm.company_id = c.id ';
        $query .= '  AND c.id NOT IN (' . implode(',', $exclusionBindArr) . ') ';
        $query .= ' WHERE ';
        $query .= '   cm.started_at BETWEEN :from AND :to ';
        $query .= '   AND cm.region_id = :regionId ';
        $query .= '   AND cm.channel_id IN (' . implode(',', $cannnelsBindArr) . ') ';
        $query .= '   AND cm.genre_id <> 40001 ';
        $query .= ' GROUP BY ';
        $query .= '   c.name ';
        $query .= ' ORDER BY ';
        $query .= '   grp DESC NULLS LAST';
        $query .= '   , total_count ASC';
        $query .= '   , company_name ASC';
        $query .= ' LIMIT 10; ';

        $result = $this->select($query, $bindings);
        return json_decode(json_encode($result), true);
    }

    public function findCmRankingOfProduct(Carbon $from, Carbon $to, int $regionId, array $channelIds, array $broadcasterCompanyIds)
    {
        $bindings = [];
        $bindings[':from'] = $from->toDateTimeString();
        $bindings[':to'] = $to->toDateTimeString();
        $bindings[':regionId'] = $regionId;
        $cannnelsBindArr = $this->createArrayBindParam('channelIds', ['channelIds' => $channelIds], $bindings);
        $exclusionBindArr = $this->createArrayBindParam('exclusionCompanyIds', ['exclusionCompanyIds' => $broadcasterCompanyIds], $bindings);

        $query = '';
        $query .= ' SELECT  ';
        $query .= '   c.name company_name ';
        $query .= '   , pr.name product_name ';
        $query .= '   , SUM(round(cm.household_viewing_rate * (cm.duration::real / 15 ), 1))::real personal_point ';
        $query .= ' , count(*) total_count ';
        $query .= ' FROM ';
        $query .= '    commercials cm ';
        $query .= '  INNER JOIN ';
        $query .= '    companies c ';
        $query .= '  ON ';
        $query .= '    cm.company_id = c.id ';
        $query .= '    AND c.id NOT IN (' . implode(',', $exclusionBindArr) . ') ';
        $query .= ' INNER JOIN ';
        $query .= '   products pr ';
        $query .= ' ON ';
        $query .= '   cm.product_id = pr.id  ';
        $query .= ' WHERE ';
        $query .= '   cm.started_at BETWEEN :from AND :to ';
        $query .= '   AND cm.region_id = :regionId ';
        $query .= '   AND cm.channel_id IN (' . implode(',', $cannnelsBindArr) . ') ';
        $query .= "   AND cm.genre_id <> '40001' ";
        $query .= ' GROUP BY ';
        $query .= '   c.name ';
        $query .= '   ,pr.name ';
        $query .= '   ,cm.genre_id ';
        $query .= ' ORDER BY ';
        $query .= '   personal_point DESC NULLS LAST ';
        $query .= '   , total_count ASC ';
        $query .= '   , company_name ASC ';
        $query .= '   , product_name ASC ';
        $query .= ' LIMIT 10; ';

        $result = $this->select($query, $bindings);
        return json_decode(json_encode($result), true);
    }
}
