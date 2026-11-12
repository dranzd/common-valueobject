<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\DateTime;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Webmozart\Assert\InvalidArgumentException;

/**
 * A time of day.
 *
 * The value is kept as a string, exactly as given, and has to be accepted by PHP's `DateTime::createFromFormat()`
 * with the format `H:i:s`. That parser is lenient: overflowing values such as `24:00:00` or `10:61:00` are accepted
 * and stored unchanged. Passing `null` uses the current time in the default timezone.
 */
class Time extends Literal
{
    private const DATE_FORMAT = 'H:i:s';

    /**
     * @throws InvalidArgumentException when the value cannot be parsed with the format `H:i:s`.
     */
    public function __construct(string|null $value = null)
    {
        if (null === $value) {
            $value = \date(self::DATE_FORMAT);
        }

        if (!self::isFormatValid($value)) {
            throw new InvalidArgumentException(
                \sprintf('Expected a time in the format "%s", got "%s".', self::DATE_FORMAT, $value)
            );
        }

        $this->value = $value;
    }

    /**
     * Tells whether the given value is a string that `DateTime::createFromFormat()` can parse with the format
     * `H:i:s`; anything that is not a string is not valid.
     *
     * @param mixed $datetime
     */
    final public static function isFormatValid($datetime): bool
    {
        return \is_string($datetime) && false !== \DateTime::createFromFormat(self::DATE_FORMAT, $datetime);
    }
}
