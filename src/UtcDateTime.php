<?php declare(strict_types=1);

namespace Darsyn\DateTime;

class UtcDateTime extends DateTime
{
    /** {@inheritdoc} */
    public function __construct(?string $datetime = null, ?\DateTimeZone $timezone = null)
    {
        $datetime = new \DateTime($datetime ?: 'now', $timezone);
        $datetime->setTimezone($tz = new \DateTimeZone('UTC'));
        parent::__construct($datetime->format('Y-m-d\TH:i:s.000000\Z'), $tz);
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        return $this->format(DateTimeInterface::UTC_DATETIME);
    }
}
