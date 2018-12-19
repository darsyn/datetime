<?php declare(strict_types=1);

namespace Darsyn\DateTime\Doctrine;

use Darsyn\DateTime\UtcDateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * IP fields will be stored as a string in the database and converted back to
 * the IP value object when querying.
 */
class UtcDateType extends Type
{
    public const NAME = 'utcdatetime';

    /** {@inheritdoc} */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDateTimeTypeDeclarationSQL([]);
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof UtcDateTime) {
            return $value;
        }
        if (empty($value)) {
            return null;
        }
        try {
            return new UtcDateTime($value);
        } catch (\Exception $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }
        if ($value instanceof UtcDateTime) {
            return (string) $value;
        }
        if ($value instanceof \DateTimeInterface) {
            return (string) UtcDateTime::createFromObject($value);
        }
        if (\is_string($value)) {
            try {
                return (string) (new UtcDateTime($value));
            } catch (\Exception $e) {
                throw ConversionException::conversionFailed($value, self::NAME);
            }
        }
        throw ConversionException::conversionFailedInvalidType($value, 'string', [
            UtcDateTime::class,
            \DateTimeInterface::class,
            'string'
        ]);
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return self::NAME;
    }

    /** {@inheritdoc} */
    public function getBindingType()
    {
        return \PDO::PARAM_STR;
    }

    /** {@inheritdoc} */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
