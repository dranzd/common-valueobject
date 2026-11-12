<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\DateTime;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\DateTime\Time;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreDefaultTimezoneTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(Time::class)]
#[UsesClass(Literal::class)]
#[UsesTrait(CompareSameValueTrait::class)]
final class TimeTest extends TestCase
{
    use CoreDefaultTimezoneTrait;

    private const ZONES = ['UTC', 'America/New_York', 'Europe/London', 'Australia/Lord_Howe', 'Pacific/Kiritimati'];
    private const FORMAT = 'H:i:s';

    #[DataProvider('provideValidValues')]
    public function testAcceptsValidValue(string $value): void
    {
        $object = new Time($value);

        self::assertSame($value, $object->toNative());
        self::assertTrue(Time::isFormatValid($value));
        self::assertSame($value, Time::fromNative($value)->toNative());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidValues(): iterable
    {
        yield 'last second' => ['23:59:59'];
        yield 'midnight' => ['00:00:00'];
        yield 'noon' => ['12:00:00'];
    }

    /**
     * The check is PHP's lenient parser, so these are accepted and stored exactly as given.
     */
    #[DataProvider('provideLenientlyAcceptedValues')]
    public function testAcceptsValuesPhpParsesLeniently(string $value): void
    {
        self::assertTrue(Time::isFormatValid($value));
        self::assertSame($value, (new Time($value))->toNative());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideLenientlyAcceptedValues(): iterable
    {
        yield 'hour 24' => ['24:00:00'];
        yield 'hour overflow' => ['25:00:00'];
        yield 'minute overflow' => ['10:61:00'];
    }

    #[DataProvider('provideInvalidValues')]
    public function testRejectsUnparsableValue(string $value): void
    {
        self::assertFalse(Time::isFormatValid($value));

        try {
            new Time($value);
            self::fail('Expected an exception.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame(InvalidArgumentException::class, $exception::class);
            self::assertSame(
                \sprintf('Expected a time in the format "%s", got "%s".', self::FORMAT, $value),
                $exception->getMessage()
            );
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidValues(): iterable
    {
        yield 'garbage' => ['garbage'];
        yield 'empty' => [''];
        yield 'trailing data' => ['23:59:59 x'];
        yield 'leading space' => [' 23:59:59'];
        yield 'unpadded' => ['1:2:3'];
        yield 'no seconds' => ['10:00'];
        yield 'date given' => ['2024-02-03'];
    }

    #[DataProvider('providePercentSigns')]
    public function testMessageIsNotFormattedTwice(string $value, string $expectedMessage): void
    {
        try {
            new Time($value);
            self::fail('Expected an exception.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame($expectedMessage, $exception->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function providePercentSigns(): iterable
    {
        $prefix = 'Expected a time in the format "H:i:s", got "';

        yield 'trailing percent' => ['100%', $prefix . '100%".'];
        yield 'conversion specification' => ['%s', $prefix . '%s".'];
        yield 'percent with text' => ['50% off', $prefix . '50% off".'];
    }

    public function testFromNativeEnforcesTheSameCheck(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Time::fromNative('garbage');
    }

    public function testIsFormatValidReturnsFalseForNonStrings(): void
    {
        self::assertFalse(Time::isFormatValid(null));
        self::assertFalse(Time::isFormatValid(20240203));
        self::assertFalse(Time::isFormatValid([]));
        self::assertFalse(Time::isFormatValid(new \stdClass()));
    }

    /**
     * The previous release's check, kept here to prove the result is identical for every string.
     */
    #[DataProvider('provideDifferentialInputs')]
    public function testIsFormatValidMatchesThePreviousReleaseInAnyTimezone(string $value): void
    {
        $previous = \date_default_timezone_get();
        $answers = [];

        try {
            foreach (self::ZONES as $zone) {
                \date_default_timezone_set($zone);
                $answers[$zone] = Time::isFormatValid($value);

                self::assertSame(false !== \DateTime::createFromFormat(self::FORMAT, $value), $answers[$zone], $zone);
            }
        } finally {
            \date_default_timezone_set($previous);
        }

        self::assertCount(1, \array_unique($answers));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideDifferentialInputs(): iterable
    {
        yield 'row 0' => ['2024-02-29'];
        yield 'row 1' => ['1999-12-31'];
        yield 'row 2' => ['2024-02-30'];
        yield 'row 3' => ['2023-02-29'];
        yield 'row 4' => ['2024-1-5'];
        yield 'row 5' => ['2024-13-01'];
        yield 'row 6' => ['0000-00-00'];
        yield 'row 7' => ['9999-99-99'];
        yield 'row 8' => ['-2024-01-01'];
        yield 'row 9' => ['2024-02-29 x'];
        yield 'row 10' => [' 2024-02-29'];
        yield 'row 11' => ['2024-02-03 10:00:00'];
        yield 'row 12' => ['2024-02-29 23:59:59'];
        yield 'row 13' => ['2024-02-30 10:00:00'];
        yield 'row 14' => ['2024-02-03 25:00:00'];
        yield 'row 15' => ['2024-02-03 10:61:00'];
        yield 'row 16' => ['2024-2-3 1:2:3'];
        yield 'row 17' => ['2024-03-10 02:30:00'];
        yield 'row 18' => ['2024-11-03 01:30:00'];
        yield 'row 19' => ['2024-02-03'];
        yield 'row 20' => ['23:59:59'];
        yield 'row 21' => ['00:00:00'];
        yield 'row 22' => ['24:00:00'];
        yield 'row 23' => ['25:00:00'];
        yield 'row 24' => ['10:61:00'];
        yield 'row 25' => ['99:99:99'];
        yield 'row 26' => ['1:2:3'];
        yield 'row 27' => ['10:00'];
        yield 'row 28' => ['10:00:00 '];
        yield 'row 29' => [''];
        yield 'row 30' => ['garbage'];
        yield 'row 31' => ['100%'];
        yield 'row 32' => ['%s'];
        yield 'row 33' => ['2024-02-29
'];
        yield 'row 34' => ['٢٠٢٤-01-01'];
    }

    public function testDefaultsToNowInTheDefaultTimezone(): void
    {
        self::withDefaultTimezone('Pacific/Kiritimati', function (): void {
            $before = new \DateTimeImmutable();
            $object = new Time();
            $after = new \DateTimeImmutable();

            self::assertTimeBetween($object->toNative(), $before, $after);
            self::assertNotSame(\gmdate('H'), \substr($object->toNative(), 0, 2));
        });
    }

    /**
     * Compares times of day only, and copes with a run that crosses midnight.
     */
    private static function assertTimeBetween(string $value, \DateTimeImmutable $from, \DateTimeImmutable $to): void
    {
        $start = $from->format('H:i:s');
        $end = $to->format('H:i:s');
        $inside = $start <= $end ? ($value >= $start && $value <= $end) : ($value >= $start || $value <= $end);

        self::assertTrue($inside, \sprintf('%s is not between %s and %s', $value, $start, $end));
    }

    public function testBehavesLikeALiteral(): void
    {
        $object = new Time('23:59:59');

        self::assertFalse($object->isEmpty());
        self::assertTrue($object->sameValueAs(new Time('23:59:59')));
        self::assertFalse($object->sameValueAs(new Time('00:00:00')));
        self::assertSame('Literal value object of ' . '23:59:59', (string) $object);
    }
}
