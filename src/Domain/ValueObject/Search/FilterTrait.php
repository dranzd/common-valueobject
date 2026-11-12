<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * Supplies the Filter half of the Criteria contract for a concrete filter class.
 *
 * The using class must still implement sameValueAs(), __toString() and the static
 * fromCodeAndValueOrNull() itself, and should define a FILTER_CODE constant naming the filter.
 */
trait FilterTrait
{
    public function isForFiltering(): bool
    {
        return true;
    }

    public function isForSorting(): bool
    {
        return false;
    }

    /**
     * Whether the other object is an instance of exactly the same class as this one.
     */
    public function sameClassAs(object $object): bool
    {
        return get_class($this) === get_class($object);
    }
}
