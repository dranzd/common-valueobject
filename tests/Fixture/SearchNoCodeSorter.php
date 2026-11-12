<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Search\Sorter;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;

/**
 * Sorter that forgot to define FILTER_CODE: it can never be built from a code.
 */
class SearchNoCodeSorter implements Sorter
{
    use SorterTrait;

    private int $order;

    public function __construct(int $order)
    {
        $this->order = $order;
    }

    public function __toString(): string
    {
        return 'nocode:' . $this->order;
    }
}
