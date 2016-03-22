<?php

namespace Date;

use DateTime;

class Jalali extends DateAbstract
{
    /**
     * @var int
     */
    protected $jYear;

    /**
     * @var int
     */
    protected $jMonth;

    /**
     * @var int
     */
    protected $jDay;

    /**
     * @param string|null $time
     * @param mixed $tz
     */
    public function __construct($time = null, $tz = null)
    {
        parent::__construct(null, self::safeCreateDateTimeZone($tz));
        $this->decode($time);
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return void
     */
    public function setDate($year, $month, $day = 1)
    {
        $this->jYear = (int) $year;
        $this->jMonth = (int) $month;
        $this->jDay = (int) $day;

        list($year, $month, $day) = $this->convertToGregorian($year, $month, $day);
        parent::setDate($year, $month, $day);
    }

    /**
    * Decode a datetime string
    * Supported formats:
    *  (1395/05/04) (1395-5-04) (1395-01-04 23:54) (1395-01-04 23:54:10) (23:54) (23:54:10)
    *
    * @param string $time
    * @return void
    */
    protected function decode($time)
    {
        $datetimeDetailRegex = '/(\d{1,4})[\/|-](\d{1,2})[\/|-](\d{1,2})\s(\d{1,2}):(\d{1,2}):(\d{1,2})/';
        $datetimeRegex = '/(\d{4})[\/|-](\d{1,2})[\/|-](\d{1,2})\s(\d{1,2}):(\d{1,2})/';
        $dateRegex = '/(\d{1,4})[\/|-](\d{1,2})[\/|-](\d{1,2})/';
        $timeDetailRegex = '/(\d{1,2}):(\d{1,2}):(\d{1,2})/';
        $timeRegex = '/(\d{1,2}):(\d{1,2})/';

        preg_match($datetimeDetailRegex, $time, $output);
        if (!empty($output)) {
            $this->setDate($output[1], $output[2], $output[3]);
            $this->setTime($output[4], $output[5], $output[6]);
            return;
        }

        preg_match($datetimeRegex, $time, $output);
        if (!empty($output)) {
            $this->setDate($output[1], $output[2], $output[3]);
            $this->setTime($output[4], $output[5]);
            return;
        }

        preg_match($dateRegex, $time, $output);
        if (!empty($output)) {
            $this->setDate($output[1], $output[2], $output[3]);
            return;
        }

        preg_match($timeDetailRegex, $time, $output);
        if (!empty($output)) {
            $this->setTime($output[1], $output[2], $output[3]);
            $this->refreshJalali();
            return;
        }

        preg_match($timeRegex, $time, $output);
        if (!empty($output)) {
            $this->setTime($output[1], $output[2]);
            $this->refreshJalali();
            return;
        }

        $this->refreshJalali();
    }

    /**
     * Refresh jalali date based on parent date
     *
     * @return void
     */
    protected function refreshJalali()
    {
        list($this->jYear, $this->jMonth, $this->jDay) = $this->convertToJalali(parent::format('Y'), parent::format('m'), parent::format('d'));
    }

    /**
     * @return \Date\Date
     */
    public function toGregorian()
    {
        return new Date(parent::format(parent::ATOM), parent::getTimezone());
    }

    /**
     * An aliases for toGregorian
     *
     * @return \Date\Date
     */
    public function tog()
    {
        return $this->toGregorian();
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
                'day' => 'd'
            )):
                return $this->format($formats[$name]);
                break;
        }
    }

    /**
     * Convert datetime format to its actual values
     *
     * @param string $format
     * @return mixed
     */
    public function format($format)
    {
        $symbols = array('Y', 'm', 'd', 'H', 'i', 's');
        $intactSymbols = array('H', 'i', 's');

        $findSymbolsRegex = '/('.implode('|', $symbols).')(-|:|\s|\d|\z|\/)/';
        $symbols = preg_match_all($findSymbolsRegex, $format, $symbols) ? $symbols[1] : array();

        foreach ($symbols as $symbol) {
            $v = '';
            switch ($symbol) {
                case 'Y':
                    $v = sprintf('%04d', $this->jYear);
                    break;

                case 'm':
                    $v = sprintf('%02d', $this->jMonth);
                    break;

                case 'd':
                    $v = sprintf('%02d', $this->jDay);
                    break;

                default:
                    if (in_array($symbol, $intactSymbols)) {
                        $v = parent::format($symbol);
                    }
                    break;
            }

            $format = preg_replace("/$symbol/", $v, $format);
        }

        return $format;
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
     * @return \Date\Jalali
     */
    public static function now($tz = null)
    {
        return new static(null, $tz);
    }

    /**
     * Create base date
     *
     * @param int $year
     * @param int $month
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
        list($year, $month, $day) = self::toGregorian($year, $month, $day);

        return new static("$year-$month-$day $hour:$minute:$second", $tz);
    }

    /**
     * Convert to gregorian date
     *
     * @param int $gYear
     * @param int $gMonth
     * @param int $gDay
     * @return array
     *
     * @source https://github.com/sallar/jDateTime
     * @author Roozbeh Pournader and Mohammad Toossi
     */
    protected static function convertToGregorian($jYear, $jMonth, $jDay)
    {
        $gDaysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $jDaysInMonth = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        if (is_null($jYear))
            $jYear = 1395;

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
        $leap = true;

        if ($gDayNo >= 36525) {
            $gDayNo--;
            $gYear += 100 * self::div($gDayNo,  36524);
            $gDayNo = $gDayNo % 36524;
            if ($gDayNo >= 365)
                $gDayNo++;
            else
                $leap = false;
        }

        $gYear += 4 * self::div($gDayNo, 1461);
        $gDayNo %= 1461;

        if ($gDayNo >= 366) {
            $leap = false;
            $gDayNo--;
            $gYear += self::div($gDayNo, 365);
            $gDayNo = $gDayNo % 365;
        }

        for ($i = 0; $gDayNo >= $gDaysInMonth[$i] + ($i == 1 && $leap); $i++)
            $gDayNo -= $gDaysInMonth[$i] + ($i == 1 && $leap);

        $gMonth = $i + 1;
        $gDay = $gDayNo + 1;

        return array($gYear, $gMonth, $gDay);
    }


    /**
     * Convert to jalali date
     *
     * @param int $gYear
     * @param int $gMonth
     * @param int $gDay
     * @return array
     *
     * @source https://github.com/sallar/jDateTime
     * @author Roozbeh Pournader and Mohammad Toossi
     */
    protected static function convertToJalali($gYear, $gMonth, $gDay)
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
