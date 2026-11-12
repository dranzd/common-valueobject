<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Search\Sorter;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;

/**
 * Sorter written the loose way consumers wrote them: an untyped `$order` set from whatever the caller passes.
 */
class SearchLooseSorter implements Sorter
{
    use SorterTrait;

    public const FILTER_CODE = 'loose';

    /**
     * @var mixed
     */
    private $order;

    public function __construct(mixed $order)
    {
        $this->order = $order;
    }

    public function __toString(): string
    {
        return 'loose:' . \print_r($this->order, true);
    }
}
