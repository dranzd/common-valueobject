<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Search;

use Dranzd\Common\Domain\ValueObject\Search\Sorter;
use Dranzd\Common\Domain\ValueObject\Search\SorterTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchCreatedSorter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchLooseSorter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameFilter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNameSorter;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNoCodeSorter;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversTrait(SorterTrait::class)]
#[UsesClass(SearchNameSorter::class)]
#[UsesClass(SearchCreatedSorter::class)]
#[UsesClass(SearchNoCodeSorter::class)]
final class SorterTraitTest extends TestCase
{
    public function testFactoriesBuildTheUsingClassWithTheOrder(): void
    {
        $asc = SearchNameSorter::inAscendingOrder();
        $desc = SearchCreatedSorter::inDescendingOrder();

        self::assertInstanceOf(SearchNameSorter::class, $asc);
        self::assertSame(SORT_ASC, $asc->getOrder());
        self::assertInstanceOf(SearchCreatedSorter::class, $desc);
        self::assertSame(SORT_DESC, $desc->getOrder());
    }

    public function testIsForSortingOnly(): void
    {
        $sorter = SearchNameSorter::inAscendingOrder();

        self::assertTrue($sorter->isForSorting());
        self::assertFalse($sorter->isForFiltering());
    }

    public function testFromCodeAndValueBuildsForMatchingCodeAndIntegerOrder(): void
    {
        $asc = SearchNameSorter::fromCodeAndValueOrNull('name', SORT_ASC);
        $desc = SearchNameSorter::fromCodeAndValueOrNull('name', SORT_DESC);

        self::assertNotNull($asc);
        self::assertSame(SORT_ASC, $asc->getOrder());
        self::assertNotNull($desc);
        self::assertSame(SORT_DESC, $desc->getOrder());
    }

    public function testFromCodeUsesTheConstantOfTheCalledClass(): void
    {
        self::assertNull(SearchCreatedSorter::fromCodeAndValueOrNull('name', SORT_ASC));
        self::assertInstanceOf(
            SearchCreatedSorter::class,
            SearchCreatedSorter::fromCodeAndValueOrNull('created', SORT_ASC)
        );
    }

    public function testFromCodeReturnsNullForOtherCode(): void
    {
        self::assertNull(SearchNameSorter::fromCodeAndValueOrNull('other', SORT_ASC));
    }

    public function testFromCodeReturnsNullWithoutFilterCodeConstant(): void
    {
        self::assertNull(SearchNoCodeSorter::fromCodeAndValueOrNull('name', SORT_ASC));
    }

    #[DataProvider('unmatchedValues')]
    public function testFromCodeRequiresExactIntegerOrder(mixed $value): void
    {
        self::assertNull(SearchNameSorter::fromCodeAndValueOrNull('name', $value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function unmatchedValues(): array
    {
        return [
            'string 4' => ['4'],
            'string 3' => ['3'],
            'string asc' => ['asc'],
            'float 4.0' => [4.0],
            'bool true' => [true],
            'null' => [null],
            'other int' => [1],
        ];
    }

    public function testFromCodeBuildsAClassThatDefinesTheCodeItself(): void
    {
        $sorter = new class (SORT_ASC) extends SearchNoCodeSorter {
            public const FILTER_CODE = 'x';
        };

        self::assertNotNull($sorter::fromCodeAndValueOrNull('x', SORT_ASC));
        self::assertNull($sorter::fromCodeAndValueOrNull('y', SORT_ASC));
    }

    public function testFactoriesCoerceTheOrderForAStringTypedConstructor(): void
    {
        // The trait has no strict_types on purpose: new $class(SORT_ASC) hands the int 4 to a string parameter.
        // Declared in eval() because static analysis rightly objects to this kind of class.
        $sorter = eval(<<<'PHP'
            return new class ('4') implements \Dranzd\Common\Domain\ValueObject\Search\Sorter {
                use \Dranzd\Common\Domain\ValueObject\Search\SorterTrait;

                private string $order;

                public function __construct(string $order)
                {
                    $this->order = $order;
                }

                public function __toString(): string
                {
                    return 'order ' . $this->order;
                }
            };
            PHP);
        self::assertInstanceOf(Sorter::class, $sorter);

        $ascending = $sorter::inAscendingOrder();

        self::assertSame(SORT_ASC, $ascending->getOrder());
        self::assertSame('order 4', (string) $ascending);
        self::assertSame(SORT_DESC, $sorter::inDescendingOrder()->getOrder());
    }

    public function testGetOrderCoercesAnUntypedNumericStringOrder(): void
    {
        // Same reason: getOrder(): int is evaluated without strict_types, so '4' comes back as 4.
        $sorter = new SearchLooseSorter('4');

        self::assertSame(SORT_ASC, $sorter->getOrder());
        self::assertSame(SORT_DESC, (new SearchLooseSorter(SORT_DESC))->getOrder());
    }

    public function testSameValueAsComparesTheStoredOrderStrictly(): void
    {
        self::assertFalse((new SearchLooseSorter('4'))->sameValueAs(new SearchLooseSorter(4)));
        self::assertTrue((new SearchLooseSorter('4'))->sameValueAs(new SearchLooseSorter('4')));
    }

    public function testSameValueAs(): void
    {
        $asc = SearchNameSorter::inAscendingOrder();

        self::assertTrue($asc->sameValueAs(SearchNameSorter::inAscendingOrder()));
        self::assertFalse($asc->sameValueAs(SearchNameSorter::inDescendingOrder()));
        self::assertFalse($asc->sameValueAs(SearchCreatedSorter::inAscendingOrder()));
        self::assertFalse($asc->sameValueAs(new SearchNameFilter('name')));
    }

    public function testSameValueAsIsFalseForASubclassWithTheSameOrder(): void
    {
        self::assertFalse(
            SearchNameSorter::inAscendingOrder()->sameValueAs(SearchCreatedSorter::inAscendingOrder())
        );
        self::assertFalse(
            SearchCreatedSorter::inAscendingOrder()->sameValueAs(SearchNameSorter::inAscendingOrder())
        );
    }

    public function testSameClassAsIsExact(): void
    {
        $sorter = new SearchNameSorter(SORT_ASC);

        self::assertTrue($sorter->sameClassAs(SearchNameSorter::inDescendingOrder()));
        self::assertFalse($sorter->sameClassAs(SearchCreatedSorter::inAscendingOrder()));
    }

    public function testToStringIsProvidedByTheUsingClass(): void
    {
        self::assertSame('name:3', (string) SearchNameSorter::inDescendingOrder());
    }
}
