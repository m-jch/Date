<?php

namespace Date;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

abstract class DateAbstract extends DateTime
{
    /**
     * Return string datetime wherever echo object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format('Y/m/d H:i:s');
    }

    /**
     * Create an instance object for current datetime
     *
     * @param mixed $tz
     * @return \Date\Jalali|\Date\Date
     */
    public static function now($tz = null)
    {
        return new static(null, $tz);
    }

    /**
     * Create base datetime
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param string $timezone
     * @return mixed
     */
    public static function create($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $tz = null)
    {
        return new static("$year-$month-$day $hour:$minute:$second", $tz);
    }

    /**
     * Create base date
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $timezone
     * @return mixed
     */
    public static function createDate($year = null, $month = null, $day = null, $tz = null)
    {
        return new static("$year-$month-$day", $tz);
    }

    /**
     * Create base time
     *
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param string $timezone
     * @return mixed
     */
    public static function createTime($hour = null, $minute = null, $second = null, $tz = null)
    {
        return new static("$hour:$minute:$second", $tz);
    }

    /**
    * Creates a DateTimeZone from a string, DateTimeZone or integer offset.
    *
    * @param \DateTimeZone|string|int|null $object
    * @return \DateTimeZone
    *
    * @throws InvalidArgumentException
    *
    * @source https://github.com/briannesbitt/Carbon
    */
   protected static function safeCreateDateTimeZone($object)
   {
       if ($object === null) {
           // Don't return null... avoid Bug #52063 in PHP <5.3.6
           return new DateTimeZone(date_default_timezone_get());
       }
       if ($object instanceof DateTimeZone) {
           return $object;
       }
       if (is_numeric($object)) {
           $timezone_offset = $object * 3600;
           $tzName = timezone_name_from_abbr(null, $timezone_offset, true);
           if ($tzName === false) {
               throw new InvalidArgumentException("Unknown or bad timezone ($object)");
           }
           $object = $tzName;
       }
       $tz = @timezone_open((string) $object);
       if ($tz === false) {
           throw new InvalidArgumentException("Unknown or bad timezone ($object)");
       }
       return $tz;
   }
}
