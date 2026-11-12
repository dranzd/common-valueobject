<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Money\Basic;

/**
 * Consumer-style subclass of Money\Basic that adds behaviour and relies on the protected members.
 */
class CompositePrice extends Basic
{
    public function describe(): string
    {
        return $this->amount->toNative() . ' ' . $this->currency->toNative();
    }
}
