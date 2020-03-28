<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class SponsorDao extends Dao
{
    public function sponsorBasic(int $sponsorId): ?\stdClass
    {
        $select = [
            's.id',
            's.name',
            's.disp_name',
            's.deleted_at',
            's.created_at',
            's.status',
            's.started_at',
            's.ended_at',
            'sr.permissions AS permissions',
            'st.settings AS trial_settings',
        ];

        $from = [
            'sponsors AS s',
            'INNER JOIN sponsor_roles AS sr ON sr.sponsor_id = s.id',
            'LEFT OUTER JOIN sponsor_trials AS st ON st.sponsor_id = s.id',
        ];

        $where = 's.id = :sponsor_id';

        $query = sprintf(
            "SELECT %s FROM %s WHERE ${where}",
            implode(',', $select),
            implode(' ', $from),
            $where
        );

        $record = $this->selectOne($query, [
            ':sponsor_id' => $sponsorId,
        ]);

        if ($record === null) {
            return null;
        }

        if (property_exists($record, 'permissions')) {
            $record->permissions = json_decode($record->permissions);
        }

        if (property_exists($record, 'trial_settings')) {
            $record->trial_settings = json_decode($record->trial_settings);
        }

        return $record;
    }
}
