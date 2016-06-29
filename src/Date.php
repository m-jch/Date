<?php

namespace Date;

use DateTime;

class Date extends DateAbstract
{
    public function __construct($time = null, $tz = null)
    {
        if ($this->newFromTimestamp($time)) {
            parent::__construct(null, self::safeCreateDateTimeZone($tz));
            $this->setTimestamp($time);
        } else {
            parent::__construct($time, self::safeCreateDateTimeZone($tz));
        }
    }

    /**
     * Check if class initialize from timestamp
     *
     * @param mixed $time
     * @return bool
     */
    protected function newFromTimestamp($time)
    {
        $timestampRegex = '/\A\d+\z/';
        preg_match($timestampRegex, $time, $output);
        if (!empty($output)) {
            return true;
        }

        return false;
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

    /**
     * Equivalent to new Date()
     *
     * @return \Date\Date
     */
    public static function make($time)
    {
        return new Date($time);
    }
}
