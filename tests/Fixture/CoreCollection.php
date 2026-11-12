<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Collection\Collection;

/**
 * A consumer-style collection.
 *
 * @extends Collection<int, mixed>
 */
class CoreCollection extends Collection
{
    public function countItems(): int
    {
        return $this->count();
    }
}
