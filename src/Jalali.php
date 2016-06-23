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
     * Output formats
     */
    const EN = 0;
    const FA = 1;

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
     * @var int
     */
    protected $outputFormat = self::EN;

    /**
     * Day names based on l symobl
     *
     * @var array
     */
    protected $format_l = array(self::SUNDAY => 'یکشنبه', self::MONDAY => 'دوشنبه', self::TUESDAY => 'سه‌شنبه', self::WEDNESDAY => 'چهارشنبه', self::THURSDAY => 'پنج‌شنبه', self::FRIDAY => 'جمعه', self::SATURDAY => 'شنبه');

    /**
     * Day names based on D symobl
     *
     * @var array
     */
    protected $format_D = array(self::SUNDAY => 'یکش', self::MONDAY => 'دوش', self::TUESDAY => 'سشن', self::WEDNESDAY => 'چها', self::THURSDAY => 'پنج', self::FRIDAY => 'جمع', self::SATURDAY => 'شنب');

    /**
     * Month names
     *
     * @var array
     */
    protected $format_F = array('فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند');

    /**
     * Month names
     *
     * @var array
     */
    protected $format_M = array('فرو', 'ارد', 'خرد', 'تیر', 'مرد', 'شهر', 'مهر', 'آبا', 'آذر', 'دی', 'بهم', 'اسف');

    /**
     * @param string|null $time
     * @param mixed $tz
     */
    public function __construct($time = null, $tz = null)
    {
        parent::__construct(null, self::safeCreateDateTimeZone($tz));
        $this->decode($this->faToEn($time));
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

        return $this;
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
        $datetimeDetailRegex = '/(\d{1,4})[\/|-](\d{1,2})[\/|-](\d{1,2})\s*(\d{1,2}):(\d{1,2}):(\d{1,2})/';
        $datetimeRegex = '/(\d{4})[\/|-](\d{1,2})[\/|-](\d{1,2})\s*(\d{1,2}):(\d{1,2})/';
        $dateRegex = '/(\d{1,4})[\/|-](\d{1,2})[\/|-](\d{1,2})/';
        $timeDetailRegex = '/(\d{1,2}):(\d{1,2}):(\d{1,2})/';
        $timeRegex = '/(\d{1,2}):(\d{1,2})/';
        $timestampRegex = '/\A\d+\z/';

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

        preg_match($timestampRegex, $time, $output);
        if (!empty($output)) {
            $this->setTimestamp($output[0]);
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
                'day' => 'd',
                'daysInMonth' => 't'
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
        $symbols = array('Y', 'm', 'd', 'D', 'H', 'i', 's', 'l', 'j', 'N', 'w', 'z', 'W', 'F', 'M', 'n', 't', 'L', 'o', 'y', 'a', 'A', 'B', 'g', 'G', 'h', 's', 'u', 'e', 'i', 'I', 'O', 'P', 'T', 'U', 'c', 'r');
        $intactSymbols = array('H', 'i', 's', 'N', 'w', 'B', 'g', 'G', 'h', 's', 'u', 'e', 'i', 'I', 'O', 'P', 'T', 'U', 'c', 'r');

        $findSymbolsRegex = '/('.implode('|', $symbols).')(-|:|\s|\d|\z|\/)/';
        $symbols = preg_match_all($findSymbolsRegex, $format, $symbols) ? $symbols[1] : array();

        foreach ($symbols as $symbol) {
            $v = '';
            switch ($symbol) {
                case 'Y':
                    $v = sprintf('%04d', $this->jYear);
                    break;

                case 'y':
                    $v = $this->jYear % 100;
                    break;

                case 'm':
                    $v = sprintf('%02d', $this->jMonth);
                    break;

                case 'd':
                    $v = sprintf('%02d', $this->jDay);
                    break;

                case 'D':
                    $v = $this->format_D[parent::format('w')];
                    break;

                case 'l':
                    $v = $this->format_l[parent::format('w')];
                    break;

                case 'j':
                    $v = sprintf('%01d', $this->jMonth);
                    break;

                case 'z':
                    $v = $this->dayOfYear();
                    break;

                case 'W':
                    $v = $this->weekOfYear();
                    break;

                case 'F':
                    $v = $this->format_F[$this->jMonth - 1];
                    break;

                case 'M':
                    $v = $this->format_M[$this->jMonth - 1];
                    break;

                case 'n':
                    $v = sprintf('%01d', $this->jMonth);
                    break;

                case 't':
                    if ($this->jMonth < 7) $v = 31;
                    elseif ($this->jMonth == 12 || $this->leap) $v = 30;
                    else $v = 29;
                    break;

                case 'L':
                    $v = (int) $this->leap;
                    break;

                case 'o':
                    $v = $this->jYear;
                    break;

                case 'a':
                    $v = parent::format('H') > 12 ? 'ب.ظ' : 'ق.ظ';
                    break;

                case 'A':
                    $v = parent::format('H') > 12 ? 'بعد از ظهر' : 'قبل از ظهر';
                    break;

                default:
                    if (in_array($symbol, $intactSymbols)) {
                        $v = parent::format($symbol);
                    }
                    break;
            }

            $format = preg_replace("/$symbol/", $v, $format);
        }

        if ($this->outputFormat == self::FA)
            return $this->enToFa($format);

        return $format;
    }

    /**
     * Return day of year
     *
     * @return int
     */
    protected function dayOfYear()
    {
        if ($this->jMonth > 6) {
            return 186 + (($this->jMonth - 6 - 1) * 30) + $this->jDay;
        }
        else {
            return (($this->jMonth - 1) * 31) + $this->jDay;
        }
    }

    /**
     * Return week of year
     *
     * @return int
     */
    protected function weekOfYear()
    {
        $dayOfYear = $this->dayOfYear();
        if (is_int($dayOfYear / 7)) {
            return $dayOfYear / 7;
        } else {
            return intval($dayOfYear / 7) + 1;
        }
    }

    /**
     * Set output format to fa
     *
     * @return $this
     */
    public function fa($format = null)
    {
        $this->outputFormat = self::FA;

        if (is_null($format)) return $this;

        return $this->format($format);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subYears($value = 1)
    {
        parent::subYears($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addYears($value = 1)
    {
        parent::addYears($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subMonths($value = 1)
    {
        parent::subMonths($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addMonths($value = 1)
    {
        parent::addMonths($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subWeeks($value = 1)
    {
        parent::subWeeks($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addWeeks($value = 1)
    {
        parent::addWeeks($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subDays($value = 1)
    {
        parent::subDays($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addDays($value = 1)
    {
        parent::addDays($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subHours($value = 1)
    {
        parent::subHours($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addHours($value = 1)
    {
        parent::addHours($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subMinutes($value = 1)
    {
        parent::subMinutes($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addMinutes($value = 1)
    {
        parent::addMinutes($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function subSeconds($value = 1)
    {
        parent::subSeconds($value);
        $this->refreshJalali();

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function addSeconds($value = 1)
    {
        parent::addSeconds($value);
        $this->refreshJalali();

        return $this;
    }
}
