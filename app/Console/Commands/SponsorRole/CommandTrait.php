<?php

namespace App\Console\Commands\SponsorRole;

trait CommandTrait
{
    protected $minDatetime = '1900-01-01 00:00:00';

    protected $maxDatetime = '9999-12-31 23:59:59';

    protected $descriptionForInteractive = '引数を指定しない場合、対話モードで実行され、最後に登録内容を確認し yes することで更新することができます。';

    protected function setSignature(array $arguments): void
    {
        $s = '';

        foreach ($arguments as $val) {
            $s .= '{' . $val . '}';
            $s .= ' ';
        }

        $this->signature = sprintf(
            '%s %s',
            $this->signature,
            trim($s)
        );
    }

    protected function argumentsSponsorId(): int
    {
        if (empty($this->argument('sponsor_id'))) {
            $sponsorId = $this->ask('sponsor_idを数値形式で入力してください');

            if (empty($sponsorId) || !is_numeric($sponsorId)) {
                $this->error('sponsor_idは数値形式で入力必須です');
                exit(1);
            }

            return $sponsorId;
        }

        return $this->argument('sponsor_id');
    }

    protected function argumentsPermissions(): array
    {
        if (empty($this->argument('permissions'))) {
            $ask = $this->ask('permissionsをjson形式で入力してください（空の場合はデフォルト値になります）');
            $permissions = json_decode($ask, true);

            if ($permissions !== null && !is_array($permissions)) {
                $this->error('permissionsはjson形式で入力してください');
                exit(1);
            }

            if (count($permissions) < 1) {
                $permissions = [];

                foreach (\Config::get('permission.list') as $key => $val) {
                    if (preg_match('/^smart2::/', $key)) {
                        $permissions[$key] = [
                            'contract' => [
                                'start' => $this->minDatetime,
                                'end' => $this->maxDatetime,
                            ],
                        ];
                    } else {
                        $permissions[$key] = [];
                    }
                }
            }

            return $permissions;
        }

        return $this->argument('permissions');
    }

    protected function argumentsPermissionKey(): string
    {
        if (empty($this->argument('permission_key'))) {
            $permissionKey = $this->ask('permission_keyを入力してください');

            /*
            if (!preg_match('/^[a-z0-9_]+::[a-z0-9_]+::[a-z0-9_]+$/', $permissionKey)) {
                $this->error('permission_keyは"/^[a-z0-9_]+::[a-z0-9_]+::[a-z0-9_]+$/"の形式で入力してください');
                exit(1);
            }
            */

            return $permissionKey;
        }

        return $this->argument('permission_key');
    }

    protected function argumentsUpdatePermissions(): array
    {
        if (empty($this->argument('update_permissions'))) {
            $ask = $this->ask('update_permissionsをjson形式で入力してください');
            $updatePermissions = json_decode($ask, true);

            if ($updatePermissions === null || !is_array($updatePermissions)) {
                $this->error('update_permissionsをjson形式で入力してください');
                exit(1);
            }

            return $updatePermissions;
        }

        return $this->argument('update_permissions');
    }

    protected function confirmApply(string $type, array $messages = []): void
    {
        switch ($type) {
            case 'insert':
                $confirmMessage = '追加しますか？';
                break;
            case 'update':
                $confirmMessage = '更新しますか？';
                break;
            case 'delete':
                $confirmMessage = '削除しますか？';
                break;
            default:
                throw new \InvalidArgumentException("Unknown type: \"{$type}\"");
                break;
        }

        foreach ($messages as $val) {
            $this->info($val);
        }

        if (!$this->confirm($confirmMessage)) {
            $this->info('キャンセルしました');
            exit(0);
        }
    }

    protected function infoApplied(): void
    {
        $this->info('適用しました');
    }
}
