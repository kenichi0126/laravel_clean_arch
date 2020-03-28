<?php

namespace App\DataAccess\SettingAttrDivs\Delete;

use App\DataProxy\AttrDivsInterface as AttrDivs;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\DataAccessInterface;

/**
 * Class DataAccess.
 */
final class DataAccess implements DataAccessInterface
{
    private $attrDivs;

    /**
     * DataAccess constructor.
     * @param AttrDivs $attrDivs
     */
    public function __construct(AttrDivs $attrDivs)
    {
        $this->attrDivs = $attrDivs;
    }

    /**
     * @param string $division
     * @param string $code
     */
    public function __invoke(string $division, string $code): void
    {
        $this->attrDivs->deleteByDivisionAndCode($division, $code);
    }
}
