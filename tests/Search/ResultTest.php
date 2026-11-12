<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Search;

use Dranzd\Common\Domain\ValueObject\Search\Result;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchArrayItem;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\SearchNativeItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Result::class)]
final class ResultTest extends TestCase
{
    public function testExposesItemsAndTotal(): void
    {
        $result = new Result(['a', 'b'], 42);

        self::assertSame(['a', 'b'], $result->getItems());
        self::assertSame(42, $result->getTotal());
    }

    public function testKeysAreKept(): void
    {
        self::assertSame(['k' => 'v'], (new Result(['k' => 'v'], 1))->getItems());
        self::assertSame(['items' => ['k' => 'v'], 'total' => 1], (new Result(['k' => 'v'], 1))->toArray());
    }

    public function testToArrayCarriesTheTotal(): void
    {
        self::assertSame(['items' => ['a'], 'total' => 42], (new Result(['a'], 42))->toArray());
    }

    public function testToArrayLeavesScalarsAndPlainArraysAlone(): void
    {
        $result = new Result(['text', 5, null, ['nested' => [1, 2]]], 4);

        self::assertSame(
            ['items' => ['text', 5, null, ['nested' => [1, 2]]], 'total' => 4],
            $result->toArray()
        );
    }

    public function testToArrayUsesToArrayBeforeToNative(): void
    {
        $result = new Result([new SearchArrayItem('x')], 1);

        self::assertSame(['items' => [['name' => 'x']], 'total' => 1], $result->toArray());
    }

    public function testToArrayUsesToNativeWhenThereIsNoToArray(): void
    {
        $result = new Result([new SearchNativeItem('y')], 1);

        self::assertSame(['items' => ['native-y'], 'total' => 1], $result->toArray());
    }

    public function testToArrayKeepsKeysAndPassesOtherObjectsThrough(): void
    {
        $plain = new stdClass();
        $result = new Result(['first' => new SearchArrayItem('z'), 'second' => $plain], 2);

        $array = $result->toArray();

        self::assertSame(['name' => 'z'], $array['items']['first']);
        self::assertSame($plain, $array['items']['second']);
    }

    public function testToArrayConvertsOnlyOneLevel(): void
    {
        $inner = new SearchArrayItem('deep');
        $result = new Result([['wrapped' => $inner]], 1);

        self::assertSame($inner, $result->toArray()['items'][0]['wrapped']);
    }

    public function testEmptyResult(): void
    {
        self::assertSame(['items' => [], 'total' => 0], (new Result([], 0))->toArray());
    }
}
