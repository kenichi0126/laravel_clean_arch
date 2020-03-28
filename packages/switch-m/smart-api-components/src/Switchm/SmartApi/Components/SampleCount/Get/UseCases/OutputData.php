<?php

namespace Switchm\SmartApi\Components\SampleCount\Get\UseCases;

class OutputData
{
    private $cnt;

    private $editFlg;

    /**
     * OutputData constructor.
     * @param array $cnt
     * @param bool $editFlg
     */
    public function __construct(array $cnt, bool $editFlg)
    {
        $this->cnt = $cnt;
        $this->editFlg = $editFlg;
    }

    /**
     * @return array
     */
    public function cnt(): array
    {
        return $this->cnt;
    }

    /**
     * @return bool
     */
    public function editFlg(): bool
    {
        return $this->editFlg;
    }
}
