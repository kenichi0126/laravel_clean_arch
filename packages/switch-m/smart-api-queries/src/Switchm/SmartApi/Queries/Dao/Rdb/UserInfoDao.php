<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

class UserInfoDao extends Dao
{
    public function getUserInfo(int $memberId): \stdClass
    {
        $query =
            'select ' .
            '    mem.id, ' .
            "    family_name || ' ' || given_name as name, " .
            '   sp.id as sponsor_id, ' .
            '    sp.name as sponsor_name, ' .
            '    sp.disp_name as sponsor_disp_name, ' .
            '    mem.email, ' .
            '    mem.login_control_flag, ' .
            '    COALESCE(mss.conv_15_sec_flag,1) conv_15_sec_flag, ' .
            "    COALESCE(mss.aggregate_setting, 'ga12') aggregate_setting, " .
            '    COALESCE(mss.aggregate_setting_region_id,1) aggregate_setting_region_id, ' .
            '    mss.aggregate_setting_code, ' .
            '    mem.started_at, ' .
            '    mem.init_login_flag, ' .
            '    mem.ended_at ' .
            'from members mem ' .
            '    left outer join member_system_settings mss on ' .
            '        mem.id = mss.member_id ' .
            '    left outer join sponsors sp on ' .
            '        mem.sponsor_id = sp.id ' .
            'where ' .
            '    mem.id = :member_id';

        return $this->selectOne($query, [
            ':member_id' => $memberId,
        ]);
    }
}
