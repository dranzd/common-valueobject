<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Native\Number\Real;

/**
 * A consumer-style real number value object.
 */
class CoreRatio extends Real
{
    public function raw(): float
    {
        return $this->value;
    }
}
