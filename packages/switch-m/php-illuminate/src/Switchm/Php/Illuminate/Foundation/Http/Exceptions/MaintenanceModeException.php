<?php

namespace Switchm\Php\Illuminate\Foundation\Http\Exceptions;

use Exception;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException as BaseMaintenanceModeException;

class MaintenanceModeException extends BaseMaintenanceModeException
{
    /**
     * Is it a partial maintenance.
     *
     * @var bool
     */
    public $isPartially;

    /**
     * Create a new exception instance.
     *
     * @param int $time
     * @param int $retryAfter
     * @param string $message
     * @param bool $isPartially
     * @param \Exception $previous
     * @param int $code
     */
    public function __construct($time, $retryAfter = null, $message = null, $isPartially = false, Exception $previous = null, $code = 0)
    {
        parent::__construct($time, $retryAfter, $message, $previous, $code);

        $this->isPartially = $isPartially;
    }

    /**
     * Get isPartially.
     *
     * @return bool
     */
    public function getIsPartially(): bool
    {
        return $this->isPartially;
    }
}
