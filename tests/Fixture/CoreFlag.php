<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Native\Logic\Boolean;

/**
 * A consumer-style boolean value object.
 */
class CoreFlag extends Boolean
{
    public function raw(): bool
    {
        return $this->value;
    }
}
