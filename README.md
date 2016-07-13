# Date

Fix all needs to date in php

## Install

Add below line to ```require``` section in ```composer.json```:

```
"m-jch/date" : "~1"
```

## Tutorial

This package contains 2 class, ```Jalali``` and ```Date```. ```Jalali``` class responsible for Jalali (shamsi) date time and ```Date``` class responsible for Gregorian date time.

All below examples should be run in ```Jalali``` or ```Date``` class.

All format listed in PHP [```date```](http://php.net/manual/en/function.date.php) function support.

We want to implements most of Carbon PHP class.

### Include Classes

```php
use Date\Date;
use Date\Jalali;
```

### Examples

#### Creators

```php
$date = new Jalali('1395/04/10 23:10:05');
$date = new Jalali('۱۳۹۵/۰۴/۱۰ ۲۳:۱۰:۰۵');
$date->format('Y-m-d H:i:s');

echo new Jalali('1395-04-10');

// You can use make static method instead of new instance
// Below examples has same results
$date = (new Jalali('1395/04/10 23:10:05'))->subDays(4);
$date = Jalali::make('1395/04/10 23:10:05')->subDays(4);

echo Jalali::now();
echo Jalali::yesterday();
echo Jalali::tomorrow();

echo Jalali::create(1394, 05, 04, 12, 45, 23);
echo Jalali::createDate(1394, 05, 04);
echo Jalali::createTime(12, 45, 23);
```

#### Converters

```php
// Jalali to Gregorian
$date = new Jalali('1373/06/05 23:10:05');
echo $date->toGregorian();

$date = new Jalali('1373/06/05 23:10:05');
echo $date->tog()->format('Y-m'); // An aliases for toGregorian method

// Gregorian to Jalali
$date = new Date('2012-06-05 20:05:01');
echo $date->toJalali();

$date = new Date('2012-06-05 20:05:01');
echo $date->toj(); // An aliases for toJalali method
```

#### Modifiers

Consider you can send negative number to ```add*``` methods, instead of using ```sub*``` methods.

```php
echo Jalali::now()->startOfMonth();
echo Jalali::now()->endOfMonth();

echo Jalali::now()->startOfDay();
echo Jalali::now()->endOfDay();

echo Jalali::now()->addYears(2);
echo Jalali::now()->subYears(1);

echo Jalali::now()->addMonths(2);
echo Jalali::now()->subMonths(1);

echo Jalali::now()->addWeeks(3);
echo Date::now()->subWeeks(3);

echo Jalali::now()->addDays(1);
echo Jalali::now()->subDays(5);

echo Jalali::now()->addHours(2);
echo Jalali::now()->subHours(1);

echo Jalali::now()->addMinutes(10);
echo Jalali::now()->subMinutes(5);

echo Jalali::now()->addSeconds(14);
echo Jalali::now()->subSeconds(50);
```

#### Customize

```php
// echo as farsi numbers
echo Jalali::now()->fa()->subDays(4);
echo (new Jalali)->addDays(5)->fa('Y-m-d l'); // Can use just fa() instead of fa()->format()
```

#### Comparisons

All comparisons based on Gregorian date, so you can compare two date with different type of class.

```php
$date1 = new Jalali('1395-07-12');
$date2 = new Jalali('1395-10-05');

$date1->equalTo($date2);
$date1->eq($date2);

$date1->notEqualTo($date2);
$date1->ne($date2);

$date1->greaterThan($date2);
$date1->gt($date2);

$date1->greaterThanOrEqualTo($date2);
$date1->gte($date2);

$date1->lessThan($date2);
$date1->lt($date2);

$date1->lessThanOrEqualTo($date2);
$date1->lte($date2);
```

#### Differences

You can not send first parameter or send it null, for calculate difference from current datetime.

If second parameter is true, return base on absolute, otherwise base on difference, default is true

We're not sure about ```diffInYears```, ```diffInMonths```, ```diffInWeeks``` and ```diffInDays``` in Jalali date, if you have any bug, please report it.

```php
$date1->diffInYears($date2);
$date1->diffInMonths($date2);
$date1->diffInWeeks($date2);
$date1->diffInDays();
$date1->diffInHours($date2);
$date1->diffInMinutes($date2, true);
$date1->diffInSeconds($date2, false);
```

#### Timestamp

```php
// Create from timestamp
$date = new Jalali(1466664181);
$date = new Date(1466664181);
$date = Jalali::createFromTimestamp(1466664181);

// Get timestamp
$date->getTimestamp();
$date->format('U');

// Set timestamp
$date->setTimestamp(1466664181);
```

## Frameworks

### Laravel5

Add below codes to ```aliases``` array in ```app.php``` config file.

```php
'Jalali' => Date\Jalali::class,
'Date'   => Date\Date::class
```
