<?php

namespace Switchm\Php\Illuminate\Database;

use Closure;
use Exception;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Database\QueryException;
use Switchm\Php\Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class PostgresConnection extends BasePostgresConnection
{
    private const CONNECTION_ERROR_MAINTENANCE_MESSAGE = "現在データベースのメンテナンスを行なっております。\nご迷惑をおかけしますが、再開までしばらくお待ちください。";

    protected function runQueryCallback($query, $bindings, Closure $callback)
    {
        try {
            $result = $callback($query, $bindings);
        } catch (Exception $e) {
            if ($e->getCode() === 7) {
                $time = null;
                $retry = null;
                $message = self::CONNECTION_ERROR_MAINTENANCE_MESSAGE;
                $isPartially = false;
                throw new MaintenanceModeException($time, $retry, $message, $isPartially);
            }

            throw new QueryException(
                $query,
                $this->prepareBindings($bindings),
                $e
            );
        }

        return $result;
    }
}
