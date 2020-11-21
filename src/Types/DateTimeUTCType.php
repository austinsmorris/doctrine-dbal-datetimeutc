<?php

declare(strict_types=1);

namespace ASM\Doctrine\DBAL\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

use function date_create;

class DateTimeUTCType extends DateTimeType
{
    public const DATETIMEUTC = 'datetimeutc';

    public function getName(): string
    {
        return self::DATETIMEUTC;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null
            ? null
            : $value->setTimezone(new DateTimeZone('UTC'))->format($platform->getDateTimeFormatString());
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTime) {
            return $value->setTimezone(new DateTimeZone('UTC'));
        }

        $dateTime = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, new DateTimeZone('UTC'));

        if ($dateTime === false) {
            $dateTime = date_create($value, new DateTimeZone('UTC'));
        }

        if ($dateTime === false) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $dateTime;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
