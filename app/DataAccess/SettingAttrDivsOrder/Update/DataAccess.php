<?php

namespace App\DataAccess\SettingAttrDivsOrder\Update;

use App\DataProxy\AttrDivsInterface as AttrDivs;
use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\DataAccessInterface;

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
     * @param array $divisions
     */
    public function __invoke(array $divisions): void
    {
        foreach ($divisions as $division => $codes) {
            foreach ($codes as $order => $code) {
                if ($code !== null) {
                    $this->attrDivs->updateByDivisionAndCode($division, $code, ['display_order' => $order + 1]);
                }
            }
        }
    }
}
