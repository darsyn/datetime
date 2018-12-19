<?php declare(strict_types=1);

namespace Darsyn\DateTime\Tests;

use Darsyn\DateTime\Date;
use Darsyn\DateTime\DateTimeInterface;
use PHPUnit\Framework\TestCase as Test;

class DateTest extends Test
{
    public function dataProviderValidConstructionValues(): array
    {
        return [
            ['2018-05-09T14:18:00+02:00', 'America/Los_Angeles', '2018-05-09'],
            // Bangkok does not have daylight savings time so this test *should* always be correct (ish; until some
            // politicians decide to change it).
            ['2018-01-01T00:00:00', 'Asia/Bangkok', '2018-01-01'],
            ['2018-01-01T00:00:00-05:00', 'Asia/Bangkok', '2018-01-01'],
            ['2018-01-01T00:00:00-05:00', null, '2018-01-01'],

        ];
    }

    public function dataProviderValidFormatValues(): array
    {
        return [
            ['Y-m-d\TH:i:sP', '2018-05-09T14:18:00+02:00', 'America/Los_Angeles', '2018-05-09'],
            // Perth does not have daylight savings time so this test *should* always be correct (ish; until some
            // politicians decide to change it).
            ['l, jS F, Y (g:ia)', 'Wednesday, 9th May, 2018 (3:34pm)', 'Australia/Perth', '2018-05-09'],
        ];
    }

    /** @dataProvider dataProviderValidConstructionValues */
    public function testValidDates(string $value, ?string $timezone, string $expected): void
    {
        $timezone = is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $date = new Date($value, $timezone);
        Test::assertSame($expected, (string) $date);
    }

    /** @dataProvider dataProviderValidFormatValues */
    public function testValidFormats(string $format, string $value, ?string $timezone, string $expected): void
    {
        $timezone = is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $date = Date::createFromFormat($format, $value, $timezone);
        Test::assertSame($expected, (string) $date);
    }

    public function testNoConstructorArgumentsIndicateCurrentDate(): void
    {
        $base = new \DateTime;
        // We *MUST* sleep here, because UtcDateTime will ignore the microseconds part of the date meaning that any
        // date greater by less than a second will still be reported as less than.
        sleep(1);
        $date = new Date;
        // The UtcDate has it's time component stripped so it *should* be less than the current datetime, though this
        // test will fail if the base date was less than a second away from midnight.
        Test::assertLessThan($base, $date);
    }

    public function testUtcDateDoesNotUseTime(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $date = new Date;
            Test::assertSame('00:00:00', $date->format('H:i:s'));
        }
    }

    public function testUtcDateDoesNotUseMicroseconds(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $date = new Date;
            Test::assertSame('000000', $date->format('u'));
        }
    }

    public function testDateSerialisesIntoJsonNicelyUnlikeThePhpVersionWhichDoesntBecauseItDoesntLikeYou(): void
    {
        $date = new Date;
        // Ensure that it's the same as the string representation.
        Test::assertSame(json_encode((string) $date), \json_encode($date));
        // Ensure that it's in standardized UTC format.
        Test::assertSame(json_encode($date->format(DateTimeInterface::UTC_DATE)), \json_encode($date));
    }
}
