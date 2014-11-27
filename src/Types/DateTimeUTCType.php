<?php

namespace ASM\Doctrine\DBAL\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Class DateTimeUTCType
 * @package ASM\Doctrine\DBAL\Types
 */
class DateTimeUTCType extends DateTimeType
{
    const DATETIMEUTC = 'datetimeutc';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::DATETIMEUTC;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return ($value !== null) ?
            $value->setTimezone(new DateTimeZone('UTC'))->format($platform->getDateTimeFormatString()) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTime) {
            return $value->setTimezone(new DateTimeZone('UTC'));
        }

        $dateTime = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, new DateTimeZone('UTC'));

        if (!$dateTime) {
            $dateTime = date_create($value, new DateTimeZone('UTC'));
        }

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $dateTime;
    }
}
