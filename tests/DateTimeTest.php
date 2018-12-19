<?php declare(strict_types=1);

namespace App\Tests\Model;

use Darsyn\DateTime\DateTime;
use Darsyn\DateTime\DateTimeInterface;
use PHPUnit\Framework\TestCase as Test;

class DateTimeTest extends Test
{
    public function dataProviderValidConstructionValues(): array
    {
        return [
            ['2018-05-09T14:18:00+02:00', 'America/Los_Angeles', '2018-05-09T14:18:00+02:00'],
            // Bangkok does not have daylight savings time so this test *should* always be correct.
            ['2018-01-01T00:00:00', 'Asia/Bangkok', '2018-01-01T00:00:00+07:00'],
            ['2018-01-01T00:00:00-05:00', 'Asia/Bangkok', '2018-01-01T00:00:00-05:00'],
            ['2018-01-01T00:00:00-05:00', null, '2018-01-01T00:00:00-05:00'],

        ];
    }

    public function dataProviderValidFormatValues(): array
    {
        return [
            ['Y-m-d\TH:i:sP', '2018-05-09T14:18:00+02:00', 'America/Los_Angeles', '2018-05-09T14:18:00+02:00'],
            // Perth does not have daylight savings time so this test *should* always be correct.
            ['l, jS F, Y (g:ia)', 'Wednesday, 9th May, 2018 (3:34pm)', 'Australia/Perth', '2018-05-09T15:34:00+08:00'],
        ];
    }

    /** @dataProvider dataProviderValidConstructionValues */
    public function testValidDates(string $value, ?string $timezone, string $expected): void
    {
        $timezone = is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $datetime = new DateTime($value, $timezone);
        Test::assertSame($expected, (string) $datetime);
    }

    /** @dataProvider dataProviderValidFormatValues */
    public function testValidFormats(string $format, string $value, ?string $timezone, string $expected): void
    {
        $timezone = is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $datetime = DateTime::createFromFormat($format, $value, $timezone);
        Test::assertSame($expected, (string) $datetime);
    }

    public function testNoConstructorArgumentsIndicateCurrentDate(): void
    {
        $base = new \DateTime;
        // We *MUST* sleep here, because UtcDateTime will ignore the microseconds part of the date meaning that any
        // date greater by less than a second will still be reported as less than.
        sleep(1);
        $datetime = new DateTime;
        Test::assertGreaterThanOrEqual($base, $datetime);
    }

    public function testUtcDateDoesNotUseMicroseconds(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $datetime = new DateTime;
            Test::assertSame('000000', $datetime->format('u'));
        }
    }

    public function testDateTimeSerialisesIntoJsonNicelyUnlikeThePhpVersionWhichDoesntBecauseItDoesntLikeYou(): void
    {
        $datetime = new DateTime;
        // Ensure that it's the same as the string representation.
        Test::assertSame(\json_encode((string) $datetime), json_encode($datetime));
        // Ensure that it's in standardized UTC format.
        Test::assertSame(json_encode($datetime->format(DateTimeInterface::RFC3339)), json_encode($datetime));
    }
}
