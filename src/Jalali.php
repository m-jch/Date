<?php

namespace Date;

use DateTime;

class Jalali extends DateAbstract
{
    /**
     * Names of the days of the week
     */
    const SUNDAY    = 0;
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;

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
     * Day names based on l symobl
     *
     * @param array
     */
    protected $format_l = array(self::SUNDAY => 'یکشنبه', self::MONDAY => 'دوشنبه', self::TUESDAY => 'سه‌شنبه', self::WEDNESDAY => 'چهارشنبه', self::THURSDAY => 'پنج‌شنبه', self::FRIDAY => 'جمعه', self::SATURDAY => 'شنبه');

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

        list($year, $month, $day) = $this->jalaliToGregorian($year, $month, $day);
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
        list($this->jYear, $this->jMonth, $this->jDay) = $this->gregorianToJalali(parent::format('Y'), parent::format('m'), parent::format('d'));
    }

    /**
     * @return \Date\Date
     */
    public function toGregorian()
    {
        return new Date(parent::format(parent::ATOM), parent::getTimezone());
    }

    /**
     * An aliases for toGregorian method
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
        $symbols = array('Y', 'm', 'd', 'H', 'i', 's', 'l');
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

                case 'l':
                    $v = $this->format_l[parent::format('w')];
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
     * @param int $days
     * @return $this
     */
    public function subDays($days)
    {
        parent::subDays($days);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $days
     * @return $this
     */
    public function addDays($days)
    {
        parent::addDays($days);
        $this->refreshJalali();

        return $this;
    }
}
