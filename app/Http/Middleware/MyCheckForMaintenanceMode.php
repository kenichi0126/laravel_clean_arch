<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use DB;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Switchm\Php\Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class MyCheckForMaintenanceMode extends CheckForMaintenanceMode
{
    private const DAILY_TRANSFER_TASK_NAME = 'Celliera:DailyDataTransfer';

    private $allowIpAddress = [
        '118.238.251.137',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->isMethod('options')) {
            if ($this->isMaintenance($request->ip())) {
                $time = null;
                $retry = null;
                $message = $this->getMaintenanceMessage();

                throw new MaintenanceModeException($time, $retry, $message);
            }

            Config::set('const.IS_RUNNING_DAILY_DATA_TRANSFER', $this->isRunningDataTransfer());
        }

        return $next($request);
    }

    private function isMaintenance(string $requestIp): bool
    {
        if (in_array($requestIp, $this->allowIpAddress)) {
            return false;
        }

        $systemInformation = DB::connection('smart_read_rdb')
            ->table('system_informations')
            ->where(['name' => Config::get('app.name')])
            ->select(['is_maintenance'])
            ->first();

        return (bool) ($systemInformation->is_maintenance);
    }

    private function isRunningDataTransfer(): bool
    {
        $recurringTask = DB::connection('smart_read_rdb')
            ->table('recurring_tasks')
            ->where(['name' => self::DAILY_TRANSFER_TASK_NAME])
            ->select(['status'])
            ->first();

        return $recurringTask->status === 'running';
    }

    private function getMaintenanceMessage(): string
    {
        $maintenanceMessage = DB::connection('smart_read_rdb')
            ->table('maintenance_messages')
            ->where(['selected' => 1])
            ->select(['message'])
            ->first();

        return $maintenanceMessage->message;
    }
}
