<?php

namespace Date;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

abstract class DateAbstract extends DateTime
{
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
