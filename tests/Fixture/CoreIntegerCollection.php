<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Collection\Collection;

/**
 * A consumer collection whose constructor validates its items, so `new static($array)` would reject some arrays.
 */
class CoreIntegerCollection extends Collection
{
    /**
     * @param array<int|string, mixed> $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!\is_int($element)) {
                throw new \InvalidArgumentException('Only integers are allowed.');
            }
        }

        parent::__construct($elements);
    }
}
