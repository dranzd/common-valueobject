<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;

/**
 * A consumer-style integer value object.
 */
class CoreCount extends Integer
{
    public function raw(): int
    {
        return $this->value;
    }
}
