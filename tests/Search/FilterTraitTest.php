<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Search;

use Dranzd\Common\Domain\ValueObject\Search\FilterTrait;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameFilter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameSorter;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(FilterTrait::class)]
#[UsesClass(SearchNameFilter::class)]
#[UsesClass(SearchNameSorter::class)]
#[UsesTrait(SorterTrait::class)]
final class FilterTraitTest extends TestCase
{
    public function testIsForFilteringOnly(): void
    {
        $filter = new SearchNameFilter('bob');

        self::assertTrue($filter->isForFiltering());
        self::assertFalse($filter->isForSorting());
    }

    public function testSameClassAsIsExact(): void
    {
        $filter = new SearchNameFilter('bob');
        $subclass = new class ('bob') extends SearchNameFilter {
        };

        self::assertTrue($filter->sameClassAs(new SearchNameFilter('alice')));
        self::assertFalse($filter->sameClassAs($subclass));
        self::assertFalse($filter->sameClassAs(SearchNameSorter::inAscendingOrder()));
    }

    public function testUsingClassSuppliesTheRest(): void
    {
        $filter = SearchNameFilter::fromCodeAndValueOrNull('name', 'bob');

        self::assertInstanceOf(SearchNameFilter::class, $filter);
        self::assertSame('name=bob', (string) $filter);
        self::assertTrue($filter->sameValueAs(new SearchNameFilter('bob')));
        self::assertFalse($filter->sameValueAs(new SearchNameFilter('alice')));
        self::assertNull(SearchNameFilter::fromCodeAndValueOrNull('other', 'bob'));
        self::assertSame('bob', $filter->getName());
    }
}
