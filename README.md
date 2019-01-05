[![Build Status](https://travis-ci.org/darsyn/datetime.svg?branch=master)](https://travis-ci.org/darsyn/datetime)

DateTime is an extension of PHP's `\DateTimeImmutable` class with additional
helper methods, and variations to deal with (a) dates containing no time
information and (b) standardized objects based on the UTC timezone.

This project aims for simplicity of use and any contribution towards that goal -
whether a bug report, modifications to the codebase, or an improvement to the
accuracy or readability of the documentation - are always welcome.

# Documentation

This library provides three classes ([`Date`](src/Date.php), 
[`DateTime`](src/DateTime.php), and [`UtcDateTime`](src/UtcDateTime.php)) and
two interfaces ([`DateInterface`](src/DateInterface.php) and
[`DateTimeInterface`](src/DateTimeInterface.php)).

[`Darsyn\DateTime\DateTimeInterface`](src/DateTimeInterface.php) (henceforth
**DTI**) provides two new helpers methods (in addition to those found on
`\DateTimeImmutable`):

- **DTI**'s `createFromObject(\DateTimeInterface $datetime): static` is the
  same as `\DateTimeImmutable::createFromMutable()` but works with any object
  that implements `\DateTimeInterface`.
- **DTI**'s `createFromTimestamp(int $timestamp): static` creates a **DTI**
  object from an integer timestamp.
  
On top of these, **DTI** objects can be stringified into a format appropriate to
the calling class, and when JSON-enoded returns that string instead of
`{"date","timezone_type","timezone"}` objects. The string formats are as
follows:

| Class         | Format           | Example                    |
|---------------|------------------|----------------------------|
| `Date`        | `Y-m-d`          | `2018-12-19`               |
| `DateTime`    | `Y-m-d\TH:i:sP`  | `2018-12-19T14:03:24+0100` |
| `UtcDateTime` | `Y-m-d\TH:i:s\Z` | `2018-12-19T13:03:24Z`     |

## Brief Example

```php
<?php

use Darsyn\DateTime\UtcDateTime;

try {
    $datetime = (string) UtcDateTime::createFromFormat(
        'l, jS F, Y (g:ia)', 
        'Wednesday, 9th May, 2018 (3:34pm)',
        new \DateTimeZone('Australia/Perth')
    );
    var_dump($datetime); // string(20) "2018-05-09T07:34:00Z"
} catch (\InvalidArgumentException $e) {
    exit('Could not construct object; value does not conform to date format.');
}
```

## Doctrine Integration

```php
<?php

use Darsyn\DateTime\Doctrine\UtcDateType;
use Doctrine\DBAL\Types\Type;

Type::addType('utc', UtcDateType::class);
```

## Symfony Parameter Converter Integration

If you are using Symfony's FrameworkExtra bundle, a parameter converter is 
included to automatically convert dates in the URL into `UtcDateTime` objects.

You need to type-hint with `Darsyn\DateTime\DateTimeInterface` (or anything that 
implements it), and the URL segment must be in UTC or RFC 3339 format (e.g. 
`2018-12-19T13:03:24Z` or `2018-12-19t14:03:24+0100`).

```yaml
services:

    darsyn.datetime.param_converter:
        class: 'Darsyn\DateTime\ParamConverter\UtcDateTimeConverter'
        tags:
            - name: 'request.param_converter'
              converter: 'darsyn_utc_converter'
```

# License

Please see the [separate license](LICENSE.md) included in this repository for a
full copy of the MIT license, which this project is licensed under.

# Authors

- [Zan Baldwin](https://zanbaldwin.com)

If you make a contribution (submit a pull request), don't forget to add your
name here!
