<?php

namespace Switchm\SmartApi\Queries\Services;

use Carbon\Carbon;
use Switchm\SmartApi\Queries\Dao\Rdb\HolidayDao;

class HolidayService
{
    /**
     * @var HolidayDao
     */
    private $holidayDao;

    /**
     * HolidayService constructor.
     * @param HolidayDao $holidayDao
     */
    public function __construct(HolidayDao $holidayDao)
    {
        $this->holidayDao = $holidayDao;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function getDateList(Carbon $start, Carbon $end): array
    {
        $holidays = array_column($this->holidayDao->findHoliday($start, $end), 'holiday');

        $list = [];

        $from = $start->copy();

        for ($i = 0; $end->gte($from); $i++) {
            $list[] = [
                'carbon' => $from->copy(),
                'date' => $from->format('Y-m-d H:i:s'),
                'holidayFlg' => in_array($from->format('Y-m-d'), $holidays),
            ];
            $from->addDay();
        }

        return $list;
    }
}
