<?php declare(strict_types=1);

namespace Darsyn\DateTime;

class DateTime extends \DateTimeImmutable implements DateTimeInterface
{
    /** {@inheritdoc} */
    public function __construct(?string $datetime = null, ?\DateTimeZone $timezone = null)
    {
        $datetime = new \DateTime($datetime ?: 'now', $timezone);
        parent::__construct($datetime->format('Y-m-d\TH:i:s.000000P'), $timezone);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public static function createFromFormat($format, $time, ?\DateTimeZone $timezone = null): self
    {
        // DateTimeImmutable's createFromFormat() method returns instances of DateTimeImmutable rather than static
        // (child class), so we'll unfortunately have to replicate some of the constructor logic here.
        if (!\is_object($datetime = \DateTime::createFromFormat($format, $time, $timezone))) {
            throw new \InvalidArgumentException('Value not compatible with date format.');
        }
        return new static($datetime->format(DateTimeInterface::NO_TIMEZONE), $datetime->getTimezone());
    }

    /** {@inheritdoc} */
    public static function createFromMutable($datetime): self
    {
        return static::createFromObject($datetime);
    }

    /** {@inheritdoc} */
    public static function createFromObject(\DateTimeInterface $datetime): DateTimeInterface
    {
        return new static($datetime->format(DateTimeInterface::NO_TIMEZONE), $datetime->getTimezone());
    }

    /** {@inheritdoc} */
    public static function createFromTimestamp(int $timestamp): DateTimeInterface
    {
        return static::createFromFormat('U', (string) $timestamp);
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        return $this->format(DateTimeInterface::RFC3339);
    }
}
