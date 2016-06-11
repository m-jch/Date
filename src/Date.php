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

        return new Jalali("$year-$month-$day $hour:$minute:$second", $this->getTimezone());
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

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch (true) {
            case array_key_exists($name, $formats = array(
                'year' => 'Y',
                'month' => 'm',
                'day' => 'd',
                'daysInMonth' => 't'
            )):
                return $this->format($formats[$name]);
                break;
        }
    }
}
