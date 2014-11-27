<?php

namespace ASM\Doctrine\DBAL\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Types\Type;
use PHPUnit_Framework_TestCase;

/**
 * Class DateTimeUTCTypeTest
 * @package ASM\Doctrine\DBAL\Types
 */
class DateTimeUTCTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    protected $abstractPlatform;

    /**
     * @var \ASM\Doctrine\DBAL\Types\DateTimeUTCType
     */
    protected $dateTimeUTCType;

    /**
     * Somebody set up us the bomb.
     */
    public function setUp()
    {
        $this->abstractPlatform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');

        if (!Type::hasType('datetimeutc')) {
            Type::addType('datetimeutc', 'ASM\Doctrine\DBAL\Types\DateTimeUTCType');
        }

        $this->dateTimeUTCType = Type::getType('datetimeutc');
    }

    public function testGetName()
    {
        $this->assertEquals('datetimeutc', $this->dateTimeUTCType->getName());
    }

    public function testConvertToDatabaseValueReturnsUTCString()
    {
        $dateTime = DateTime::createFromFormat(
            $this->abstractPlatform->getDateTimeFormatString(),
            '2014-11-27 13:16:31',
            new DateTimeZone('America/New_York')
        );

        $this->assertEquals(
            '2014-11-27 18:16:31',
            $this->dateTimeUTCType->convertToDatabaseValue($dateTime, $this->abstractPlatform)
        );
    }

    public function testConvertToDatabaseValueReturnsNullForNullValue()
    {
        $this->assertNull($this->dateTimeUTCType->convertToDatabaseValue(null, $this->abstractPlatform));
    }

    public function testConvertToPHPValueReturnsUTCDateTime()
    {
        $this->assertEquals(
            'UTC',
            $this->dateTimeUTCType->convertToPHPValue('2014-11-27 18:16:31', $this->abstractPlatform)
                ->getTimezone()->getName()
        );

        $this->assertEquals(
            '2014-11-27 13:16:31',
            $this->dateTimeUTCType->convertToPHPValue('2014-11-27 18:16:31', $this->abstractPlatform)
                ->setTimezone(new DateTimeZone('America/New_York'))
                ->format($this->abstractPlatform->getDateTimeFormatString())
        );
    }

    public function testConvertToPHPValueReturnsUTCDateTimeFromDateCreate()
    {
        $this->assertEquals(
            'UTC',
            $this->dateTimeUTCType->convertToPHPValue('Thursday, 27-Nov-2014 18:16:31', $this->abstractPlatform)
                ->getTimezone()->getName()
        );

        $this->assertEquals(
            '2014-11-27 13:16:31',
            $this->dateTimeUTCType->convertToPHPValue('Thursday, 27-Nov-2014 18:16:31', $this->abstractPlatform)
                ->setTimezone(new DateTimeZone('America/New_York'))
                ->format($this->abstractPlatform->getDateTimeFormatString())
        );
    }

    public function testConvertToPHPValueReturnsUTCDateTimeForDateTimeValue()
    {
        $dateTime = DateTime::createFromFormat(
            $this->abstractPlatform->getDateTimeFormatString(),
            '2014-11-27 13:16:31',
            new DateTimeZone('America/New_York')
        );

        $this->assertEquals(
            'UTC',
            $this->dateTimeUTCType->convertToPHPValue($dateTime, $this->abstractPlatform)->getTimezone()->getName()
        );

        $this->assertEquals(
            '2014-11-27 18:16:31',
            $this->dateTimeUTCType->convertToPHPValue($dateTime, $this->abstractPlatform)
                ->format($this->abstractPlatform->getDateTimeFormatString())
        );
    }

    public function testConvertToPHPValueReturnsNullForNullValue()
    {
        $this->assertNull($this->dateTimeUTCType->convertToPHPValue(null, $this->abstractPlatform));
    }

    public function testConvertToPHPValueThrowsConversionException()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->assertNull($this->dateTimeUTCType->convertToPHPValue('foo', $this->abstractPlatform));
    }
}
