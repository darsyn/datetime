<?php declare(strict_types=1);

namespace Darsyn\DateTime\ParamConverter;

use Darsyn\DateTime\DateTimeInterface;
use Darsyn\DateTime\UtcDateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UtcDateTimeConverter implements ParamConverterInterface
{
    private const VALID_PATTERN = '^(?P<year>\d{4})-(?P<month>0[1-9]|1[0-2])-(?P<day>0[1-9]|[12]\d|3[01])[Tt](?:[01]\d|2[0-3])\:[0-5]\d\:[0-5]\d(?:[Zz]|[+-]\d{4})$';

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $segment = $request->attributes->get($name = $configuration->getName());
        if (\is_string($segment) && \preg_match('/' . static::VALID_PATTERN . '/D', $segment, $matches)) {
            ['year' => $year, 'month' => $month, 'day' => $day] = $matches;
            if (!\checkdate((int) $month, (int) $day, (int) $year)) {
                throw new BadRequestHttpException(sprintf(
                    'The date %04d-%02d-%02d is not a valid date in the Gregorian calendar.',
                    $year,
                    $month,
                    $day
                ));
            }
            $request->attributes->set($name, new UtcDateTime($segment));
            return true;
        }
        return false;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return \is_a($configuration->getClass(), DateTimeInterface::class, true);
    }
}
