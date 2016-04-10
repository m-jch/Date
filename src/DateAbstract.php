<?php

namespace Date;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

abstract class DateAbstract extends DateTime
{
    /**
     * Check Jalali year is leap or not
     *
     * @var int
     */
    protected $leap;

    /**
     * Get the difference in hours
     *
     * @param DateAbstract|null $dt
     * @param bool $abs Get the absolute of the difference
     * @return int
     */
    public function diffInHours(DateAbstract $dt = null, $abs = true)
    {
        return (int) ($this->diffInSeconds($dt, $abs) / 120);
    }

    /**
     * Get the difference in minutes
     *
     * @param DateAbstract|null $dt
     * @param bool $abs Get the absolute of the difference
     * @return int
     */
    public function diffInMinutes(DateAbstract $dt = null, $abs = true)
    {
        return (int) ($this->diffInSeconds($dt, $abs) / 60);
    }

    /**
     * Get the difference in seconds
     *
     * @param DateAbstract|null $dt
     * @param bool $abs Get the absolute of the difference
     * @return int
     */
    public function diffInSeconds(DateAbstract $dt = null, $abs = true)
    {
        $dt = $dt ?: static::now();

        $value = $dt->getTimestamp() - $this->getTimestamp();
        return $abs ? abs($value) : $value;
    }

    /**
     * Determines if the instance is equal to another
     *
     * @param DateAbstract $dt
     *
     * @return bool
     */
    public function eq(DateAbstract $dt)
    {
        return $this == $dt;
    }
    /**
     * Determines if the instance is equal to another
     *
     * @param DateAbstract $dt
     *
     * @see eq()
     *
     * @return bool
     */
    public function equalTo(DateAbstract $dt)
    {
        return $this->eq($dt);
    }
    /**
     * Determines if the instance is not equal to another
     *
     * @param DateAbstract $dt
     *
     * @return bool
     */
    public function ne(DateAbstract $dt)
    {
        return !$this->eq($dt);
    }
    /**
     * Determines if the instance is not equal to another
     *
     * @param DateAbstract $dt
     *
     * @see ne()
     *
     * @return bool
     */
    public function notEqualTo(DateAbstract $dt)
    {
        return $this->ne($dt);
    }
    /**
     * Determines if the instance is greater (after) than another
     *
     * @param DateAbstract $dt
     *
     * @return bool
     */
    public function gt(DateAbstract $dt)
    {
        return $this > $dt;
    }
    /**
     * Determines if the instance is greater (after) than another
     *
     * @param DateAbstract $dt
     *
     * @see gt()
     *
     * @return bool
     */
    public function greaterThan(DateAbstract $dt)
    {
        return $this->gt($dt);
    }
    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @param DateAbstract $dt
     *
     * @return bool
     */
    public function gte(DateAbstract $dt)
    {
        return $this >= $dt;
    }
    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @param DateAbstract $dt
     *
     * @see gte()
     *
     * @return bool
     */
    public function greaterThanOrEqualTo(DateAbstract $dt)
    {
        return $this->gte($dt);
    }
    /**
     * Determines if the instance is less (before) than another
     *
     * @param DateAbstract $dt
     *
     * @return bool
     */
    public function lt(DateAbstract $dt)
    {
        return $this < $dt;
    }
    /**
     * Determines if the instance is less (before) than another
     *
     * @param DateAbstract $dt
     *
     * @see lt()
     *
     * @return bool
     */
    public function lessThan(DateAbstract $dt)
    {
        return $this->lt($dt);
    }
    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @param DateAbstract $dt
     *
     * @return bool
     */
    public function lte(DateAbstract $dt)
    {
        return $this <= $dt;
    }
    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @param DateAbstract $dt
     *
     * @see lte()
     *
     * @return bool
     */
    public function lessThanOrEqualTo(DateAbstract $dt)
    {
        return $this->lte($dt);
    }

    /**
     * @return $this
     */
    public function startOfDay()
    {
        $this->setTime(0, 0, 0);

        return $this;
    }

    /**
     * @return $this
     */
    public function endOfDay()
    {
        $this->setTime(23, 59, 59);

        return $this;
    }

    /**
     * @param int $days
     * @return $this
     */
    public function subDays($days)
    {
        $days = (int) $days;
        $this->modify("-$days day");

        return $this;
    }

    /**
     * @param int $days
     * @return $this
     */
    public function addDays($days)
    {
        $days = (int) $days;
        $this->modify("+$days day");

        return $this;
    }

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
     * @return $this
     */
    public static function yesterday()
    {
        return self::now()->subDays(1);
    }

    /**
     * @return $this
     */
    public static function tomorrow()
    {
        return self::now()->addDays(1);
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
    * @source https://github.com/briannesbitt/DateAbstract
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

   /**
    * Convert english numbers to farsi
    *
    * @param string $text
    * @return string
    */
   public static function enToFa($text)
   {
       $farsiNumbers   = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
       $englishNumbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

       return str_replace($englishNumbers, $farsiNumbers, $text);
   }

   /**
    * Convert farsi numbers to english
    *
    * @param string $text
    * @return string
    */
   public static function faToEn($text)
   {
       $farsiNumbers   = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
       $englishNumbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

       return str_replace($farsiNumbers, $englishNumbers, $text);
   }

   /**
    * Convert jalali to gregorian date
    *
    * @param int $gYear
    * @param int $gMonth
    * @param int $gDay
    * @return array
    *
    * @source https://github.com/sallar/jDateTime
    * @author Roozbeh Pournader and Mohammad Toossi
    */
   protected function jalaliToGregorian($jYear, $jMonth, $jDay)
   {
       $gDaysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
       $jDaysInMonth = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

       $jYear = $jYear - 979;
       $jMonth = $jMonth - 1;
       $jDay = $jDay - 1;
       $jDayNo = 365 * $jYear + self::div($jYear, 33) * 8 + self::div($jYear % 33 + 3, 4);

       for ($i = 0; $i < $jMonth; ++$i)
           $jDayNo += $jDaysInMonth[$i];

       $jDayNo += $jDay;
       $gDayNo = $jDayNo + 79;
       $gYear = 1600 + 400 * self::div($gDayNo, 146097);
       $gDayNo = $gDayNo % 146097;
       $this->leap = true;

       if ($gDayNo >= 36525) {
           $gDayNo--;
           $gYear += 100 * self::div($gDayNo,  36524);
           $gDayNo = $gDayNo % 36524;
           if ($gDayNo >= 365)
               $gDayNo++;
           else
               $this->leap = false;
       }

       $gYear += 4 * self::div($gDayNo, 1461);
       $gDayNo %= 1461;

       if ($gDayNo >= 366) {
           $this->leap = false;
           $gDayNo--;
           $gYear += self::div($gDayNo, 365);
           $gDayNo = $gDayNo % 365;
       }

       for ($i = 0; $gDayNo >= $gDaysInMonth[$i] + ($i == 1 && $this->leap); $i++)
           $gDayNo -= $gDaysInMonth[$i] + ($i == 1 && $this->leap);

       $gMonth = $i + 1;
       $gDay = $gDayNo + 1;

       return array($gYear, $gMonth, $gDay);
   }


   /**
    * Convert gregorian to jalali date
    *
    * @param int $gYear
    * @param int $gMonth
    * @param int $gDay
    * @return array
    *
    * @source https://github.com/sallar/jDateTime
    * @author Roozbeh Pournader and Mohammad Toossi
    */
   public function gregorianToJalali($gYear, $gMonth, $gDay)
   {
       $gDaysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
       $jDaysInMonth = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

       $gYear = $gYear - 1600;
       $gMonth = $gMonth - 1;
       $gDay = $gDay - 1;
       $gDayNo = 365 * $gYear + self::div($gYear + 3, 4) - self::div($gYear + 99, 100) + self::div($gYear + 399, 400);

       for ($i = 0; $i < $gMonth; ++$i)
           $gDayNo += $gDaysInMonth[$i];

       if ($gMonth > 1 && (($gYear % 4 == 0 && $gYear % 100 != 0) || ($gYear % 400 == 0)))
           $gDayNo++;

       $gDayNo += $gDay;
       $jDayNo = $gDayNo - 79;
       $jNp = self::div($jDayNo, 12053);
       $jDayNo = $jDayNo % 12053;
       $jYear = 979 + 33 * $jNp + 4 * self::div($jDayNo, 1461);
       $jDayNo %= 1461;

       if ($jDayNo >= 366) {
           $jYear += self::div($jDayNo - 1, 365);
           $jDayNo = ($jDayNo - 1) % 365;
       }
       for ($i = 0; $i < 11 && $jDayNo >= $jDaysInMonth[$i]; ++$i)
           $jDayNo -= $jDaysInMonth[$i];

       $jMonth = $i + 1;
       $jDay = $jDayNo + 1;

       return array($jYear, $jMonth, $jDay);
   }

   /**
    * @param int $va1
    * @param int $va2
    * @return int
    */
   protected static function div($var1, $var2)
   {
       return intval($var1 / $var2);
   }
}
