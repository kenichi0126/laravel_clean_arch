<?php

namespace Switchm\SmartApi\Components\UserNotice\Create\UseCases;

interface OutputBoundary
{
    /**
     * @param OutputData $output
     * @return mixed
     */
    public function __invoke(OutputData $output): void;
}
