<?php

namespace Date;

interface DateInterface
{
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
    public static function create($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $timezone = null);
}
