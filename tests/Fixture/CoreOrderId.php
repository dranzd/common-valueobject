<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Identity\Uuid;

/**
 * A typed identifier, as consumer applications declare them.
 */
class CoreOrderId extends Uuid
{
    public function describe(): string
    {
        return 'order ' . $this->value;
    }
}
