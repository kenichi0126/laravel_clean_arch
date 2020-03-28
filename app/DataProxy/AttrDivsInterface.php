<?php

namespace App\DataProxy;

/**
 * Interface AttrDivsInterface.
 */
interface AttrDivsInterface
{
    /**
     * @param string $division
     * @param string $code
     * @param array $attributes
     * @return int
     */
    public function updateByDivisionAndCode(string $division, string $code, array $attributes): int;

    /**
     * @param string $division
     * @param string $code
     */
    public function deleteByDivisionAndCode(string $division, string $code): void;
}
