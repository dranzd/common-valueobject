<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Custom\Quantity\Basic;

/**
 * Consumer-style subclass of Quantity\Basic that relies on the protected members.
 */
final class CompositeQuantity extends Basic
{
    public function describe(): string
    {
        return $this->value->toNative() . ' ' . $this->unit->toNative();
    }
}
