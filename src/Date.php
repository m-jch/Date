<?php

namespace Date;

use DateTime;

class Date extends DateAbstract
{
    public function __construct($time = null, $tz = null)
    {
        parent::__construct($time, self::safeCreateDateTimeZone($tz));
    }

    /**
     * @return \Date\Jalali
     */
    public function toJalali()
    {
        list($year, $month, $day) = $this->gregorianToJalali($this->format('Y'), $this->format('m'), $this->format('d'));
        list($hour, $minute, $second) = array($this->format('H'), $this->format('i'), $this->format('s'));

        return new Date("$year-$month-$day $hour:$minute:$second", $this->getTimezone());
    }

    /**
     * An aliases for toJalali method
     *
     * @return \Date\Jalali
     */
    public function toj()
    {
        return $this->toJalali();
    }
}
