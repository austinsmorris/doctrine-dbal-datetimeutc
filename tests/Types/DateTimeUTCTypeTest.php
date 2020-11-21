<?php

declare(strict_types=1);

namespace ASM\Doctrine\DBAL\Tests\Types;

use ASM\Doctrine\DBAL\Types\DateTimeUTCType;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPStan\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

class DateTimeUTCTypeTest extends TestCase
{
    /** @var AbstractPlatform|MockObject */
    protected $abstractPlatform;

    /** @var DateTimeUTCType */
    protected $dateTimeUTCType;

    public function setUp(): void
    {
        $this->abstractPlatform = $this->getMockForAbstractClass(AbstractPlatform::class);

        if (! Type::hasType(DateTimeUTCType::DATETIMEUTC)) {
            Type::addType(DateTimeUTCType::DATETIMEUTC, DateTimeUTCType::class);
        }

        $type = Type::getType(DateTimeUTCType::DATETIMEUTC);
        assert($type instanceof DateTimeUTCType);

        $this->dateTimeUTCType = $type;
    }

    public function testGetName(): void
    {
        self::assertEquals(DateTimeUTCType::DATETIMEUTC, $this->dateTimeUTCType->getName());
    }

    public function testConvertToDatabaseValueReturnsUTCString(): void
    {
        $dateTime = DateTime::createFromFormat(
            $this->abstractPlatform->getDateTimeFormatString(),
            '2014-11-27 13:16:31',
            new DateTimeZone('America/New_York')
        );

        self::assertEquals(
            '2014-11-27 18:16:31',
            $this->dateTimeUTCType->convertToDatabaseValue($dateTime, $this->abstractPlatform)
        );
    }

    public function testConvertToDatabaseValueReturnsNullForNullValue(): void
    {
        self::assertNull($this->dateTimeUTCType->convertToDatabaseValue(null, $this->abstractPlatform));
    }

    public function testConvertToPHPValueReturnsUTCDateTime(): void
    {
        self::assertEquals(
            'UTC',
            $this->dateTimeUTCType->convertToPHPValue('2014-11-27 18:16:31', $this->abstractPlatform)
                ->getTimezone()->getName()
        );

        self::assertEquals(
            '2014-11-27 13:16:31',
            $this->dateTimeUTCType->convertToPHPValue('2014-11-27 18:16:31', $this->abstractPlatform)
                ->setTimezone(new DateTimeZone('America/New_York'))
                ->format($this->abstractPlatform->getDateTimeFormatString())
        );
    }

    public function testConvertToPHPValueReturnsUTCDateTimeFromDateCreate(): void
    {
        self::assertEquals(
            'UTC',
            $this->dateTimeUTCType->convertToPHPValue('Thursday, 27-Nov-2014 18:16:31', $this->abstractPlatform)
                ->getTimezone()->getName()
        );

        self::assertEquals(
            '2014-11-27 13:16:31',
            $this->dateTimeUTCType->convertToPHPValue('Thursday, 27-Nov-2014 18:16:31', $this->abstractPlatform)
                ->setTimezone(new DateTimeZone('America/New_York'))
                ->format($this->abstractPlatform->getDateTimeFormatString())
        );
    }

    public function testConvertToPHPValueReturnsUTCDateTimeForDateTimeValue(): void
    {
        $dateTime = DateTime::createFromFormat(
            $this->abstractPlatform->getDateTimeFormatString(),
            '2014-11-27 13:16:31',
            new DateTimeZone('America/New_York')
        );

        self::assertEquals(
            'UTC',
            $this->dateTimeUTCType->convertToPHPValue($dateTime, $this->abstractPlatform)->getTimezone()->getName()
        );

        self::assertEquals(
            '2014-11-27 18:16:31',
            $this->dateTimeUTCType->convertToPHPValue($dateTime, $this->abstractPlatform)
                ->format($this->abstractPlatform->getDateTimeFormatString())
        );
    }

    public function testConvertToPHPValueReturnsNullForNullValue(): void
    {
        self::assertNull($this->dateTimeUTCType->convertToPHPValue(null, $this->abstractPlatform));
    }

    public function testConvertToPHPValueThrowsConversionException(): void
    {
        $this->expectException(ConversionException::class);
        self::assertNull($this->dateTimeUTCType->convertToPHPValue('foo', $this->abstractPlatform));
    }
}
