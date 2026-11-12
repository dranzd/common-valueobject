<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\DateTime;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\DateTime\DateTime;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CoreDefaultTimezoneTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(DateTime::class)]
#[UsesClass(Literal::class)]
#[UsesTrait(CompareSameValueTrait::class)]
final class DateTimeTest extends TestCase
{
    use CoreDefaultTimezoneTrait;

    private const ZONES = ['UTC', 'America/New_York', 'Europe/London', 'Australia/Lord_Howe', 'Pacific/Kiritimati'];
    private const FORMAT = 'Y-m-d H:i:s';

    #[DataProvider('provideValidValues')]
    public function testAcceptsValidValue(string $value): void
    {
        $object = new DateTime($value);

        self::assertSame($value, $object->toNative());
        self::assertTrue(DateTime::isFormatValid($value));
        self::assertSame($value, DateTime::fromNative($value)->toNative());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidValues(): iterable
    {
        yield 'leap day' => ['2024-02-29 23:59:59'];
        yield 'midnight' => ['2024-01-01 00:00:00'];
    }

    /**
     * The check is PHP's lenient parser, so these are accepted and stored exactly as given.
     */
    #[DataProvider('provideLenientlyAcceptedValues')]
    public function testAcceptsValuesPhpParsesLeniently(string $value): void
    {
        self::assertTrue(DateTime::isFormatValid($value));
        self::assertSame($value, (new DateTime($value))->toNative());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideLenientlyAcceptedValues(): iterable
    {
        yield 'overflowing day' => ['2024-02-30 10:00:00'];
        yield 'hour overflow' => ['2024-02-03 25:00:00'];
        yield 'minute overflow' => ['2024-02-03 10:61:00'];
        yield 'inside a daylight-saving gap' => ['2024-03-10 02:30:00'];
    }

    #[DataProvider('provideInvalidValues')]
    public function testRejectsUnparsableValue(string $value): void
    {
        self::assertFalse(DateTime::isFormatValid($value));

        try {
            new DateTime($value);
            self::fail('Expected an exception.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame(InvalidArgumentException::class, $exception::class);
            self::assertSame(
                \sprintf('Expected a date and time in the format "%s", got "%s".', self::FORMAT, $value),
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
        yield 'trailing data' => ['2024-02-29 23:59:59 x'];
        yield 'leading space' => [' 2024-02-29 23:59:59'];
        yield 'date only' => ['2024-02-03'];
        yield 'unpadded' => ['2024-2-3 1:2:3'];
    }

    #[DataProvider('providePercentSigns')]
    public function testMessageIsNotFormattedTwice(string $value, string $expectedMessage): void
    {
        try {
            new DateTime($value);
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
        $prefix = 'Expected a date and time in the format "Y-m-d H:i:s", got "';

        yield 'trailing percent' => ['100%', $prefix . '100%".'];
        yield 'conversion specification' => ['%s', $prefix . '%s".'];
        yield 'percent with text' => ['50% off', $prefix . '50% off".'];
    }

    public function testFromNativeEnforcesTheSameCheck(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DateTime::fromNative('garbage');
    }

    public function testIsFormatValidReturnsFalseForNonStrings(): void
    {
        self::assertFalse(DateTime::isFormatValid(null));
        self::assertFalse(DateTime::isFormatValid(20240203));
        self::assertFalse(DateTime::isFormatValid([]));
        self::assertFalse(DateTime::isFormatValid(new \stdClass()));
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
                $answers[$zone] = DateTime::isFormatValid($value);

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
            $object = new DateTime();
            $after = new \DateTimeImmutable();
            $value = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $object->toNative());

            self::assertInstanceOf(\DateTimeImmutable::class, $value);
            self::assertGreaterThanOrEqual($before->format('Y-m-d H:i:s'), $value->format('Y-m-d H:i:s'));
            self::assertLessThanOrEqual($after->format('Y-m-d H:i:s'), $value->format('Y-m-d H:i:s'));
            self::assertNotSame(\gmdate('Y-m-d H'), \substr($object->toNative(), 0, 13));
        });
    }

    public function testBehavesLikeALiteral(): void
    {
        $object = new DateTime('2024-02-29 23:59:59');

        self::assertFalse($object->isEmpty());
        self::assertTrue($object->sameValueAs(new DateTime('2024-02-29 23:59:59')));
        self::assertFalse($object->sameValueAs(new DateTime('2024-01-01 00:00:00')));
        self::assertSame('Literal value object of ' . '2024-02-29 23:59:59', (string) $object);
    }
}
