<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Search;

use Dranzd\Common\Domain\ValueObject\Search\Page;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Page::class)]
final class PageTest extends TestCase
{
    public function testConstructorKeepsNumberAndLimit(): void
    {
        $page = new Page(3, 25);

        self::assertSame(3, $page->getStartingNumber());
        self::assertSame(25, $page->getLimit());
    }

    public function testStartDefaultIsPageOneWithLimitZero(): void
    {
        $page = Page::startDefault();

        self::assertSame(1, $page->getStartingNumber());
        self::assertSame(0, $page->getLimit());
    }

    public function testConstructorRejectsPageNumberZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page number must not be zero');

        new Page(0, 10);
    }

    public function testStartFromRejectsZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page number must not be zero');

        Page::startDefault()->startFrom(0);
    }

    public function testLimitZeroIsAccepted(): void
    {
        self::assertSame(0, (new Page(1, 0))->getLimit());
        self::assertSame(0, Page::startDefault()->limitTo(0)->getLimit());
    }

    /**
     * Known gap, pinned so a change is deliberate: negatives are accepted today.
     */
    #[DataProvider('negativeValues')]
    public function testNegativeValuesAreCurrentlyAccepted(int $number, int $limit): void
    {
        $page = new Page($number, $limit);

        self::assertSame($number, $page->getStartingNumber());
        self::assertSame($limit, $page->getLimit());
        self::assertSame($number, Page::startDefault()->startFrom($number)->getStartingNumber());
        self::assertSame($limit, Page::startDefault()->limitTo($limit)->getLimit());
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function negativeValues(): array
    {
        return [
            'negative number' => [-1, 10],
            'negative limit' => [1, -5],
        ];
    }

    public function testStartFromReturnsNewInstanceAndLeavesOriginalUntouched(): void
    {
        $original = new Page(1, 10);

        $changed = $original->startFrom(4);

        self::assertNotSame($original, $changed);
        self::assertSame(1, $original->getStartingNumber());
        self::assertSame(4, $changed->getStartingNumber());
        self::assertSame(10, $changed->getLimit());
    }

    public function testLimitToReturnsNewInstanceAndLeavesOriginalUntouched(): void
    {
        $original = new Page(2, 10);

        $changed = $original->limitTo(50);

        self::assertNotSame($original, $changed);
        self::assertSame(10, $original->getLimit());
        self::assertSame(50, $changed->getLimit());
        self::assertSame(2, $changed->getStartingNumber());
    }
}
