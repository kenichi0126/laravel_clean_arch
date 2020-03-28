<?php

namespace Switchm\SmartApi\Queries\Dao;

use DB;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Expression;
use PDO;
use stdClass;

abstract class AbstractPdoDao extends AbstractDao
{
    protected function getConnectionName(): string
    {
        return $this->connectionName;
    }

    protected function getConnection(): Connection
    {
        return DB::connection($this->getConnectionName());
    }

    protected function select(string $query, array $bindings = [], bool $useReadPdo = true): array
    {
        return $this->getConnection()->select($query, $bindings, $useReadPdo);
    }

    protected function selectOne($query, $bindings = [], $useReadPdo = true): ?stdClass
    {
        $records = $this->getConnection()->select($query, $bindings, $useReadPdo);

        return array_shift($records);
    }

    protected function raw($value): Expression
    {
        return $this->getConnection()->raw($value);
    }

    protected function executeQuery(string $query): array
    {
        $stmt = $this->getConnection()->getPdo()->query($query, PDO::FETCH_OBJ);

        return $stmt->fetchAll();
    }

    protected function executeQueryOne(string $query): ?stdClass
    {
        $records = $this->executeQuery($query);

        return array_shift($records);
    }

    protected function quote(string $string, int $parameterType = null): string
    {
        return $this->getConnection()->getPdo()->quote($string, $parameterType);
    }

    protected function getEscapeValueFromArray(string $string, array $array): string
    {
        $key = array_search($this->quote($string), $array);

        return $array[$key];
    }
}
