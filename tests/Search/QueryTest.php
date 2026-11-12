<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Search;

use Dranzd\Common\Domain\ValueObject\Search\Page;
use Dranzd\Common\Domain\ValueObject\Search\Query;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameFilter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameSorter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Query::class)]
#[UsesClass(Page::class)]
#[UsesClass(SearchNameSorter::class)]
#[UsesTrait(SorterTrait::class)]
final class QueryTest extends TestCase
{
    public function testDefaultsToPageOneWithTenItemsAndNoCriteria(): void
    {
        $query = new Query();

        self::assertSame(1, $query->getPage()->getStartingNumber());
        self::assertSame(10, $query->getPage()->getLimit());
        self::assertSame([], $query->getFilters());
        self::assertSame([], $query->getSorters());
    }

    public function testConstructorKeepsGivenPageFiltersAndSorters(): void
    {
        $page = new Page(2, 5);
        $filters = [new SearchNameFilter('bob')];
        $sorters = [SearchNameSorter::inDescendingOrder()];

        $query = new Query($page, $filters, $sorters);

        self::assertSame($page, $query->getPage());
        self::assertSame($filters, $query->getFilters());
        self::assertSame($sorters, $query->getSorters());
    }

    public function testWithBuildsPageFromNumberAndItemsPerPage(): void
    {
        $filter = new SearchNameFilter('bob');
        $sorter = SearchNameSorter::inAscendingOrder();

        $query = Query::with([$filter], [$sorter], 3, 20);

        self::assertSame(3, $query->getPage()->getStartingNumber());
        self::assertSame(20, $query->getPage()->getLimit());
        self::assertSame([$filter], $query->getFilters());
        self::assertSame([$sorter], $query->getSorters());
    }

    public function testForPageNumberReturnsCloneWithoutLeakingIntoOriginal(): void
    {
        $original = new Query(new Page(1, 10));

        $changed = $original->forPageNumber(7);

        self::assertNotSame($original, $changed);
        self::assertSame(1, $original->getPage()->getStartingNumber());
        self::assertSame(7, $changed->getPage()->getStartingNumber());
        self::assertSame(10, $changed->getPage()->getLimit());
        self::assertNotSame($original->getPage(), $changed->getPage());
    }

    public function testLimitToItemsPerPageReturnsCloneWithoutLeakingIntoOriginal(): void
    {
        $original = new Query(new Page(2, 10));

        $changed = $original->limitToItemsPerPage(100);

        self::assertNotSame($original, $changed);
        self::assertSame(10, $original->getPage()->getLimit());
        self::assertSame(100, $changed->getPage()->getLimit());
        self::assertSame(2, $changed->getPage()->getStartingNumber());
    }

    public function testFilterByReplacesFiltersOnACloneOnly(): void
    {
        $old = new SearchNameFilter('old');
        $new = new SearchNameFilter('new');
        $original = new Query(null, [$old]);

        $changed = $original->filterBy([$new]);

        self::assertNotSame($original, $changed);
        self::assertSame([$new], $changed->getFilters());
        self::assertSame([$old], $original->getFilters());
        self::assertSame($original->getPage()->getLimit(), $changed->getPage()->getLimit());
    }

    public function testFilterByEmptyArrayClearsFilters(): void
    {
        $query = new Query(null, [new SearchNameFilter('x')]);

        self::assertSame([], $query->filterBy([])->getFilters());
    }

    public function testSortByReplacesSortersOnACloneOnly(): void
    {
        $old = SearchNameSorter::inAscendingOrder();
        $new = SearchNameSorter::inDescendingOrder();
        $original = new Query(null, [], [$old]);

        $changed = $original->sortBy([$new]);

        self::assertNotSame($original, $changed);
        self::assertSame([$new], $changed->getSorters());
        self::assertSame([$old], $original->getSorters());
    }

    public function testElementsAreNotValidated(): void
    {
        $arguments = self::unvalidatedArguments();

        $query = new Query(...$arguments);

        self::assertSame($arguments[1], $query->getFilters());
        self::assertSame($arguments[2], $query->getSorters());
    }

    /**
     * @return array<int, mixed>
     */
    private static function unvalidatedArguments(): array
    {
        return [null, ['anything', 1], ['else']];
    }
}
