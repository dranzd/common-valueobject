<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Exception;

/**
 * Thrown when a native argument is of a type a value object does not accept.
 */
final class InvalidNativeArgument extends \InvalidArgumentException
{
    /**
     * @param mixed $value the offending value; scalars, null and `\Stringable` objects print as their string value
     *                     (`false` and `null` print as an empty string), arrays and other objects by their type
     * @param array<array-key, string> $allowedTypes
     */
    final public function __construct($value, array $allowedTypes)
    {
        parent::__construct(
            \sprintf(
                'Argument "%s" is invalid. Allowed types for argument are "%s".',
                self::describe($value),
                \implode(', ', $allowedTypes)
            )
        );
    }

    private static function describe(mixed $value): string
    {
        if (null === $value || \is_scalar($value) || $value instanceof \Stringable) {
            return (string) $value;
        }

        return \get_debug_type($value);
    }
}
