<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Search\Criteria;
use Dranzd\Common\Domain\ValueObject\Search\Sorter;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;

/**
 * Consumer-style sorter by name; follows the SorterTrait contract (FILTER_CODE, $order, constructor).
 */
class SearchNameSorter implements Sorter
{
    use SorterTrait;

    public const FILTER_CODE = 'name';

    private int $order;

    public function __construct(int $order)
    {
        $this->order = $order;
    }

    public function __toString(): string
    {
        return self::FILTER_CODE . ':' . $this->order;
    }
}
