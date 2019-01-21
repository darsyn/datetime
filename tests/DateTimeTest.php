<?php declare(strict_types=1);

namespace Darsyn\DateTime\Tests;

use Darsyn\DateTime\DateTime;
use Darsyn\DateTime\DateTimeInterface;
use PHPUnit\Framework\TestCase as Test;

class DateTimeTest extends Test
{
    public function dataProviderValidConstructionValues(): array
    {
        return [
            // Like most central African countries, Nairobi has never observed daylight-savings so this test *should*
            // always be correct.
            ['2018-05-09T14:18:00+02:00', 'Africa/Nairobi', '2018-05-09T15:18:00+03:00', 'Africa/Nairobi'],
            // Bangkok does not have daylight savings time so this test *should* always be correct.
            ['2018-01-01T00:00:00', 'Asia/Bangkok', '2018-01-01T00:00:00+07:00', 'Asia/Bangkok'],
            ['2018-01-01T00:00:00-05:00', 'Asia/Bangkok', '2018-01-01T12:00:00+07:00', 'Asia/Bangkok'],
            ['2018-01-01T00:00:00-05:00', null, '2018-01-01T00:00:00-05:00', '-05:00'],
        ];
    }

    public function dataProviderValidFormatValues(): array
    {
        return [
            ['Y-m-d\TH:i:sP', '2018-05-09T14:18:59+02:00', 'America/Los_Angeles', '2018-05-09T05:18:59-07:00', 'America/Los_Angeles'],
            // Perth does not have daylight savings time so this test *should* always be correct.
            ['l, jS F, Y (g:ia)', 'Wednesday, 9th May, 2018 (3:34pm)', 'Australia/Perth', '2018-05-09T15:34:00+08:00', 'Australia/Perth'],
            // We must specify the timezone in the string, else the timezone will be taken from the php.ini settings
            // which would differ from environment to environment and fail the test.
            ['l, jS F, Y (g:ia) P', 'Wednesday, 9th May, 2018 (3:34pm) -11:00', null, '2018-05-09T15:34:00-11:00', '-11:00'],
        ];
    }

    public function dataProviderValidTimestampValues(): array
    {
        return [
            [1548075139, 'Africa/Nairobi', '2019-01-21T15:52:19+03:00', 'Africa/Nairobi'],
            [1500000000, 'Australia/Perth', '2017-07-14T10:40:00+08:00', 'Australia/Perth'],
            [1500000000, 'Asia/Bangkok', '2017-07-14T09:40:00+07:00', 'Asia/Bangkok'],
            [1400000000, null, null, null],
        ];
    }

    /** @dataProvider dataProviderValidConstructionValues */
    public function testValidDates(string $value, ?string $timezone, string $expectedDate, string $expectedTz): void
    {
        $timezone = is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $datetime = new DateTime($value, $timezone);
        Test::assertSame($expectedDate, (string) $datetime);
        Test::assertSame($expectedTz, $datetime->getTimezone()->getName());
    }

    /** @dataProvider dataProviderValidFormatValues */
    public function testValidFormats(
        string $format,
        string $value,
        ?string $timezone,
        string $expectedDate,
        string $expectedTz
    ): void {
        $timezone = is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $datetime = DateTime::createFromFormat($format, $value, $timezone);
        Test::assertSame($expectedDate, (string) $datetime);
        Test::assertSame($expectedTz, $datetime->getTimezone()->getName());
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

    /** @dataProvider dataProviderValidTimestampValues */
    public function testDateIsCorrectWhenTimezoneSuppliedWithTimestamp(
        int $timestamp,
        ?string $timezone,
        ?string $expectedDate,
        ?string $expectedTz
    ): void {
        $timezone = \is_string($timezone)
            ? new \DateTimeZone($timezone)
            : null;
        $datetime = DateTime::createFromTimestamp($timestamp, $timezone);
        Test::assertSame($timestamp, $datetime->getTimestamp());
        // A timestamp cannot contain any timezone information, if no timezone was set then the timezone defined in
        // php.ini is used, and as it differs depending on the environment we cannot test for it (or the resulting
        // date string which is dependant on the timezone).
        if ($timezone !== null) {
            Test::assertSame($expectedDate, (string) $datetime);
            Test::assertSame($expectedTz, $datetime->getTimezone()->getName());
        }
    }
}
