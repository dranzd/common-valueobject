<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;

/**
 * A consumer-style string value object.
 */
class CoreSku extends Literal
{
    public function upper(): string
    {
        return \strtoupper($this->value);
    }
}
