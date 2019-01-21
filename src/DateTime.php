<?php declare(strict_types=1);

namespace Darsyn\DateTime;

class DateTime extends \DateTimeImmutable implements DateTimeInterface
{
    /** {@inheritdoc} */
    public function __construct(?string $datetime = null, ?\DateTimeZone $timezone = null)
    {
        $datetime = new \DateTime($datetime ?: 'now', $timezone);
        if ($timezone instanceof \DateTimeZone) {
            $datetime->setTimezone($timezone);
        }
        parent::__construct($datetime->format(DateTimeInterface::NO_TIMEZONE), $timezone ?? $datetime->getTimezone());
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public static function createFromFormat($format, $time, ?\DateTimeZone $timezone = null): self
    {
        if (!\is_object($datetime = \DateTime::createFromFormat($format, $time, $timezone))) {
            throw new \InvalidArgumentException('Value not compatible with date format.');
        }
        return new static($datetime->format(DateTimeInterface::RFC3339), $timezone ?? $datetime->getTimezone());
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
    public static function createFromTimestamp(int $timestamp, ?\DateTimeZone $timezone = null): DateTimeInterface
    {
        return static::createFromFormat('U', (string) $timestamp, $timezone);
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
