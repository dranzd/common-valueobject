<?php

namespace Dranzd\Common\Domain\ValueObject\Search;

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

    public function sameClassAs(object $object): bool
    {
        return get_class($this) === get_class($object);
    }
}
