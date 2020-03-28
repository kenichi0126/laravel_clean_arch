<?php

namespace Switchm\Php\Illuminate\Database;

use Closure;
use Config;
use Exception;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Database\QueryException;
use Switchm\Php\Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class RedshiftConnection extends BasePostgresConnection
{
    private const DATA_TRANS_MAINTENANCE_MESSAGE = "現在集計システムへのデータ移行メンテナンスを行っております。\nご迷惑をおかけしますが、ご了承頂きますようよろしくお願い申し上げます。\n\n予定時間: am3:00 〜 am3:30 (毎日)";

    private const CONNECTION_ERROR_MAINTENANCE_MESSAGE = "現在集計システムのメンテナンスを行なっております。\nご迷惑をおかけしますが、再開までしばらくお待ちください。";

    protected function runQueryCallback($query, $bindings, Closure $callback)
    {
        if ($this->checkMaintenance()) {
            throw $this->createException(self::DATA_TRANS_MAINTENANCE_MESSAGE);
        }

        try {
            $result = $callback($query, $bindings);
        } catch (Exception $e) {
            if ($e->getCode() === 7) {
                throw $this->createException(self::CONNECTION_ERROR_MAINTENANCE_MESSAGE);
            }

            throw new QueryException(
                $query,
                $this->prepareBindings($bindings),
                $e
            );
        }

        return $result;
    }

    private function checkMaintenance(): bool
    {
        return Config::get('const.IS_RUNNING_DAILY_DATA_TRANSFER');
    }

    private function createException(string $message): MaintenanceModeException
    {
        $time = null;
        $retry = null;
        $isPartially = true;
        return new MaintenanceModeException($time, $retry, $message, $isPartially);
    }
}
