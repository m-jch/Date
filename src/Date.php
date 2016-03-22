<?php

namespace Date;

use DateTime;

class Date extends DateAbstract
{
    public function __construct($time = null, $tz = null)
    {
        parent::__construct($time, self::safeCreateDateTimeZone($tz));
    }
}
