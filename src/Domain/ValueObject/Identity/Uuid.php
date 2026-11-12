<?php

// strict_types is omitted on purpose: calls made from this class keep PHP's scalar coercion (e.g. fromNative('5')).

namespace Dranzd\Common\Domain\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Ramsey\Uuid\Uuid as BaseUuid;
use Webmozart\Assert\Assert;

/**
 * A UUID in string form. Meant to be extended to create typed identifiers (`class OrderId extends Uuid`).
 *
 * Passing `null` generates a random version 4 UUID. Any other value is checked with `Assert::uuid()` and kept
 * exactly as given. That check accepts any case, the nil UUID and the braced and `urn:uuid:` forms, but it removes
 * the characters `{` and `}` and the prefixes `urn:` and `uuid:` from anywhere in the string before matching, so
 * malformed forms such as `{550e8400-e29b-41d4-a716-446655440000` (unbalanced brace) are accepted too and are
 * stored unchanged.
 */
class Uuid extends Literal
{
    /**
     * @throws \InvalidArgumentException when the value is not a UUID.
     */
    public function __construct(string|null $value = null)
    {
        if (null === $value) {
            $value = BaseUuid::uuid4()->toString();
        }

        Assert::uuid($value);

        $this->value = $value;
    }

    public static function generateAsString(): string
    {
        return (new static())->toNative();
    }
}
