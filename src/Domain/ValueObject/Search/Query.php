<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * Immutable search request: a Page plus lists of Filter and Sorter criteria.
 *
 * Defaults to page 1 with 10 items per page and no criteria. The "with" methods return modified clones.
 * The array elements are intended to be Filter / Sorter instances but are not validated.
 */
class Query
{
    private Page $page;

    /**
     * @var array<int|string, Filter>
     */
    private array $filters;

    /**
     * @var array<int|string, Sorter>
     */
    private array $sorters;

    /**
     * @param array<int|string, Filter> $filters
     * @param array<int|string, Sorter> $sorters
     */
    public function __construct(
        Page|null $page = null,
        array $filters = [],
        array $sorters = []
    ) {
        if (null === $page) {
            $page = Page::startDefault()->startFrom(1)->limitTo(10);
        }

        $this->page    = $page;
        $this->filters = $filters;
        $this->sorters = $sorters;
    }

    /**
     * @param array<int|string, Filter> $filters
     * @param array<int|string, Sorter> $sorters
     */
    public static function with(
        array $filters,
        array $sorters,
        int $pageNumber,
        int $itemsPerPage
    ): Query {
        $page = Page::startDefault()->startFrom($pageNumber)->limitTo($itemsPerPage);

        return new self($page, $filters, $sorters);
    }

    public function forPageNumber(int $pageNumber): Query
    {
        $clone = clone $this;

        $clone->page = $clone->page->startFrom($pageNumber);

        return $clone;
    }

    public function limitToItemsPerPage(int $itemsPerPage): Query
    {
        $clone = clone $this;

        $clone->page = $clone->page->limitTo($itemsPerPage);

        return $clone;
    }

    /**
     * @param array<int|string, Filter> $filters
     */
    public function filterBy(array $filters): Query
    {
        $clone = clone $this;

        $clone->filters = $filters;

        return $clone;
    }

    /**
     * @param array<int|string, Sorter> $sorters
     */
    public function sortBy(array $sorters): Query
    {
        $clone = clone $this;

        $clone->sorters = $sorters;

        return $clone;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @return array<int|string, Filter>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array<int|string, Sorter>
     */
    public function getSorters(): array
    {
        return $this->sorters;
    }
}
