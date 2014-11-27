doctrine-dbal-datetimeutc
=========================

[![Build Status](https://travis-ci.org/austinsmorris/doctrine-dbal-datetimeutc.svg?branch=master)](https://travis-ci.org/austinsmorris/doctrine-dbal-datetimeutc)

A Doctine DBAL [Custom Mapping Type](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html) allowing the use of PHP DateTime objects automatically set to the UTC timezone.

Databases [suck at timezones](http://derickrethans.nl/storing-date-time-in-database.html).  The best way to deal with this is to store the date and time in UTC and seperately store the timezone that should be used for display purposes.  By default, PHP will create DateTime objects set the server's timezone.  This custom type overrides this to set the timezone to UTC, allowing you to later convert to the proper timezone for display.

Install via composer:

    composer require austinsmorris/doctrine-dbal-datetimeutc:~1.0
  
Add the custom type before instantiating your entity manager:

```php
\Doctrine\DBAL\Types\Type::addType('datetimeutc', 'ASM\Doctrine\DBAL\Types\DateTimeUTCType');
```

Enjoy!
