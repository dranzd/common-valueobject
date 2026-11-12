<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Search;

use Dranzd\Common\Domain\ValueObject\Search\CriteriaProviderTrait;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchCreatedSorter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchCriteriaProvider;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameFilter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameSorter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchUnconfiguredProvider;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(CriteriaProviderTrait::class)]
#[UsesClass(SearchCriteriaProvider::class)]
#[UsesClass(SearchUnconfiguredProvider::class)]
#[UsesClass(SearchNameSorter::class)]
#[UsesClass(SearchCreatedSorter::class)]
#[UsesTrait(SorterTrait::class)]
final class CriteriaProviderTraitTest extends TestCase
{
    private SearchCriteriaProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new SearchCriteriaProvider(
            [SearchNameFilter::class],
            [SearchNameSorter::class, SearchCreatedSorter::class]
        );
    }

    public function testBuildsFilterFromCodeAndValue(): void
    {
        $filter = $this->provider->filterFromCodeAndValueOrNull('name', 'bob');

        self::assertInstanceOf(SearchNameFilter::class, $filter);
        self::assertSame('bob', $filter->getName());
    }

    public function testUnknownFilterCodeGivesNull(): void
    {
        self::assertNull($this->provider->filterFromCodeAndValueOrNull('age', 'bob'));
    }

    public function testBuildsFirstMatchingSorter(): void
    {
        $name = $this->provider->sorterFromCodeAndValueOrNull('name', SORT_DESC);
        $created = $this->provider->sorterFromCodeAndValueOrNull('created', SORT_ASC);

        self::assertInstanceOf(SearchNameSorter::class, $name);
        self::assertSame(SORT_DESC, $name->getOrder());
        self::assertInstanceOf(SearchCreatedSorter::class, $created);
        self::assertSame(SORT_ASC, $created->getOrder());
    }

    public function testUnknownSorterCodeOrValueGivesNull(): void
    {
        self::assertNull($this->provider->sorterFromCodeAndValueOrNull('age', SORT_ASC));
        self::assertNull($this->provider->sorterFromCodeAndValueOrNull('name', '4'));
    }

    public function testAFilterListEntryThatBuildsASorterIsSkipped(): void
    {
        $provider = new SearchCriteriaProvider(...self::unvalidated([SearchNameSorter::class], []));

        self::assertNull($provider->filterFromCodeAndValueOrNull('name', SORT_ASC));
    }

    public function testASorterListEntryThatBuildsAFilterIsSkipped(): void
    {
        $provider = new SearchCriteriaProvider(...self::unvalidated([], [SearchNameFilter::class]));

        self::assertNull($provider->sorterFromCodeAndValueOrNull('name', 'bob'));
    }

    public function testASkippedEntryDoesNotStopTheLookup(): void
    {
        $provider = new SearchCriteriaProvider(
            ...self::unvalidated(
                [SearchNameSorter::class, SearchNameFilter::class],
                [SearchNameFilter::class, SearchNameSorter::class]
            )
        );

        self::assertInstanceOf(SearchNameFilter::class, $provider->filterFromCodeAndValueOrNull('name', 'bob'));
        self::assertInstanceOf(SearchNameSorter::class, $provider->sorterFromCodeAndValueOrNull('name', SORT_ASC));
    }

    public function testUnassignedListsAreTreatedAsEmpty(): void
    {
        $provider = new SearchUnconfiguredProvider();

        self::assertNull($provider->filterFromCodeAndValueOrNull('name', 'bob'));
        self::assertNull($provider->sorterFromCodeAndValueOrNull('name', SORT_ASC));
    }

    /**
     * Lets a test register classes of the wrong kind, which the declared list types would not allow.
     *
     * @param array<int, mixed> $filters
     * @param array<int, mixed> $sorters
     *
     * @return array<int, mixed>
     */
    private static function unvalidated(array $filters, array $sorters): array
    {
        return [$filters, $sorters];
    }
}
