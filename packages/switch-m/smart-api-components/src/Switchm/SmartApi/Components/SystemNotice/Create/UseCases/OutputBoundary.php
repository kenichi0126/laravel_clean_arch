<?php

namespace Switchm\SmartApi\Components\SystemNotice\Create\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output): void;
}
