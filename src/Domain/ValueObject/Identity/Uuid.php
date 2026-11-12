<?php

// strict_types is omitted on purpose: calls made from this class keep PHP's scalar coercion (e.g. fromNative('5')).

namespace Dranzd\Common\Domain\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Ramsey\Uuid\Uuid as BaseUuid;
use Webmozart\Assert\InvalidArgumentException;

/**
 * A UUID in string form. Meant to be extended to create typed identifiers (`class OrderId extends Uuid`).
 *
 * Passing `null` generates a random version 4 UUID. Any other value must be a UUID written in one of the accepted
 * forms, in any letter case, and is kept exactly as given:
 *
 *  - plain: `550e8400-e29b-41d4-a716-446655440000` (the nil UUID included)
 *  - braced: `{550e8400-e29b-41d4-a716-446655440000}`
 *  - URN: `urn:uuid:550e8400-e29b-41d4-a716-446655440000`
 *  - `uuid:` prefix, plain or braced: `uuid:550e8400-e29b-41d4-a716-446655440000`
 *
 * The check is done here rather than delegated, so the result does not depend on the installed version of
 * `webmozart/assert`.
 */
class Uuid extends Literal
{
    private const HEX = '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}';

    private const PATTERN = '/^(?:'
        . '(?:urn:uuid:)?' . self::HEX
        . '|(?:uuid:)?\\{' . self::HEX . '\\}'
        . '|uuid:' . self::HEX
        . ')$/D';

    /**
     * @throws \InvalidArgumentException when the value is not a UUID.
     */
    public function __construct(string|null $value = null)
    {
        if (null === $value) {
            $value = BaseUuid::uuid4()->toString();
        }

        if (\preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(\sprintf('Value "%s" is not a valid UUID.', $value));
        }

        $this->value = $value;
    }

    public static function generateAsString(): string
    {
        return (new static())->toNative();
    }
}
