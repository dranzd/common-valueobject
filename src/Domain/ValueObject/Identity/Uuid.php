<?php

// strict_types is omitted to match the parent class Literal, whose fromNative() relies on PHP's scalar coercion.

namespace Dranzd\Common\Domain\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Ramsey\Uuid\Uuid as BaseUuid;
use Webmozart\Assert\InvalidArgumentException;

/**
 * A UUID in string form. Meant to be extended to create typed identifiers (`class OrderId extends Uuid`).
 *
 * Passing `null` generates a random version 4 UUID. Any other value must be a UUID in one of these forms and is
 * kept exactly as given:
 *
 *  - plain: `550e8400-e29b-41d4-a716-446655440000` (the nil UUID included)
 *  - braced: `{550e8400-e29b-41d4-a716-446655440000}`
 *  - URN: `urn:uuid:550e8400-e29b-41d4-a716-446655440000`
 *  - `uuid:` prefix, plain or braced: `uuid:{550e8400-e29b-41d4-a716-446655440000}`
 *
 * Hex digits may be in either case; the prefixes must be lower case. The check is done here rather than delegated,
 * so the result does not depend on the installed version of `webmozart/assert`.
 */
class Uuid extends Literal
{
    private const CANONICAL = '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}';

    private const PATTERN = '/^(?:'
        . '(?:urn:uuid:|uuid:)?' . self::CANONICAL
        . '|(?:uuid:)?\\{' . self::CANONICAL . '\\}'
        . ')$/D';

    /**
     * @throws InvalidArgumentException when the value is not a UUID in one of the accepted forms.
     */
    public function __construct(string|null $value = null)
    {
        if (null === $value) {
            $value = BaseUuid::uuid4()->toString();
        }

        if (1 !== \preg_match(self::PATTERN, $value)) {
            throw new InvalidArgumentException(\sprintf('Value "%s" is not a valid UUID.', $value));
        }

        $this->value = $value;
    }

    public static function generateAsString(): string
    {
        return (new static())->toNative();
    }
}
