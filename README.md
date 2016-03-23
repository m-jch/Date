# Date [in development]

Fix all needs to date in php

## Tutorial

This package contains 2 class, ```Jalali``` and ```Date```. ```Jalali``` class responsible for Jalali (shamsi) date time and ```Date``` class responsible for Gregorian date time.

All below examples should be run in ```Jalali``` and ```Date``` class.

### Include Classes

```php
use Date\Date;
use Date\Jalali;
```

### Examples

#### Creators

```php
$date = new Jalali('1395/04/10 23:10:05');
$date->format('Y-m-d H:i:s');

echo new Jalali('1395-04-10');

echo Jalali::now();

echo Jalali::create(1394, 05, 04, 12, 45, 23);
echo Jalali::createDate(1394, 05, 04);
echo Jalali::createTime(12, 45, 23);
```

#### Converters

```php
$date = new Jalali('1373/06/05 23:10:05');
echo $date->toGregorian();

$date = new Jalali('1373/06/05 23:10:05');
echo $date->tog(); // An aliases for toGregorian method

$date = new Date('2012-06-05 20:05:01');
echo $date->toJalali();

$date = new Date('2012-06-05 20:05:01');
echo $date->toj(); // An aliases for toJalali method
```
