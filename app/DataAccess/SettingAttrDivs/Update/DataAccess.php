<?php

namespace App\DataAccess\SettingAttrDivs\Update;

use App\DataProxy\AttrDivsInterface as AttrDivs;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\DataAccessInterface;

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
     * @param array $attributes
     * @return int
     */
    public function __invoke(string $division, string $code, array $attributes): int
    {
        return $this->attrDivs->updateByDivisionAndCode($division, $code, $attributes);
    }
}
