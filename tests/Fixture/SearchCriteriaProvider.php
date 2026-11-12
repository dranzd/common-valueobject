<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Search\CriteriaProviderTrait;
use Dranzd\Common\Domain\ValueObject\Search\Filter;
use Dranzd\Common\Domain\ValueObject\Search\Sorter;

/**
 * Consumer-style provider: assigns the trait's private lists in its own constructor.
 */
class SearchCriteriaProvider
{
    use CriteriaProviderTrait;

    /**
     * @param list<class-string<Filter>> $filters
     * @param list<class-string<Sorter>> $sorters
     */
    public function __construct(array $filters, array $sorters)
    {
        $this->filters = $filters;
        $this->sorters = $sorters;
    }
}
