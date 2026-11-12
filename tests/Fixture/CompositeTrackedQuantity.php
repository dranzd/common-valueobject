<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Custom\Quantity\Basic;

/**
 * Quantity\Basic subclass overriding fromNative() to prove fromArray() binds it late and passes every element.
 */
final class CompositeTrackedQuantity extends Basic
{
    /** @var list<list<mixed>> */
    public static array $calls = [];

    protected static function fromNative(): Basic
    {
        self::$calls[] = \func_get_args();

        return parent::fromNative(...\func_get_args());
    }
}
