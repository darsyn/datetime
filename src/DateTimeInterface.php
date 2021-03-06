<?php declare(strict_types=1);

namespace Darsyn\DateTime;

interface DateTimeInterface extends \DateTimeInterface, \JsonSerializable
{
    public const UTC_DATE = 'Y-m-d';
    public const UTC_DATETIME = 'Y-m-d\TH:i:s\Z';
    public const NO_TIMEZONE = 'Y-m-d\TH:i:s';

    /**
     * @param \DateTimeInterface $datetime
     * @return \Darsyn\DateTime\DateTimeInterface
     */
    public static function createFromObject(\DateTimeInterface $datetime): self;

    /**
     * @param integer $timestamp
     * @param \DateTimeZone|null $timezone
     * @return \Darsyn\DateTime\DateTimeInterface
     */
    public static function createFromTimestamp(int $timestamp, ?\DateTimeZone $timezone = null): self;

    /**
     * @return string
     */
    public function __toString(): string;
}
