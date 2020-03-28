<?php

namespace App\DataAccess\SettingAttrDivs\Create;

use App\DataProxy\AttrDivsInterface as AttrDivs;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\DataAccessInterface;

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
     * @param array $attributes
     */
    public function __invoke(array $attributes): void
    {
        $this->attrDivs->create($attributes);
    }
}
