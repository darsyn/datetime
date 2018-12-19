<?php declare(strict_types=1);

namespace Darsyn\DateTime;

class Date extends DateTime implements DateInterface
{
    public function __construct(?string $datetime = null, ?\DateTimeZone $timezone = null)
    {
        $datetime = new \DateTime($datetime ?: 'now', $timezone);
        parent::__construct($datetime->format('Y-m-d\T00:00:00.000000\Z'), new \DateTimeZone('UTC'));
    }

    public function __toString(): string
    {
        return $this->format(DateTimeInterface::UTC_DATE);
    }
}
