<?php

namespace App\DataProxy;

use App\Eloquent\MemberSystemSettings as MemberSystemSettingsEloquent;

/**
 * Class MemberSystemSettings.
 */
class MemberSystemSettings extends BaseEloquent implements MemberSystemSettingsInterface
{
    public function __construct(MemberSystemSettingsEloquent $model)
    {
        $this->model = $model;
    }

    public function saveByMemberId(int $memberId, array $attributes): bool
    {
        $model = $this->model->find($memberId);

        if (empty($model)) {
            $model = $this->model;
            $model->member_id = $memberId;
        }
        $model->conv_15_sec_flag = $attributes['conv_15_sec_flag'];
        $model->aggregate_setting = $attributes['aggregate_setting'];
        $model->aggregate_setting_code = $attributes['aggregate_setting_code'];
        $model->aggregate_setting_region_id = $attributes['aggregate_setting_region_id'];

        return $model->save();
    }
}
