<?php

use phpunit\framework\TestCase;
use Date\Jalali;

class JalaliTest extends TestCase
{
    /****************************
    * Differents
    *****************************/

    public function testDiffInYears()
    {
        $date1 = new Jalali('1390-05-06');
        $date2 = new Jalali('1395-12-07');

        $this->assertSame(5, $date2->diffInYears($date1));
    }

    public function testDiffInYearsBaseOnDifference()
    {
        $date1 = new Jalali('1390-05-06');
        $date2 = new Jalali('1395-12-07');

        $this->assertSame(-5, $date2->diffInYears($date1, false));
    }

    /****************************
    * Timestamp
    *****************************/

    public function testTimestampInitialize()
    {
        $date = new Jalali(1466664181);

        $this->assertSame('1395-04-03 11:13:01', $date->format('Y-m-d H:i:s'));
    }

    public function testTimestampCreator()
    {
        $date = Jalali::createFromTimestamp(1466664181);

        $this->assertSame('1395-04-03 11:13:01', $date->format('Y-m-d H:i:s'));
    }

    public function testTimestampGet()
    {
        $date = new Jalali(1466664492);

        $this->assertSame(1466664492, $date->getTimestamp());
    }

    public function testTimestampFormat()
    {
        $date = new Jalali(1466664492);

        $this->assertSame('1466664492', $date->format('U'));
    }
}
