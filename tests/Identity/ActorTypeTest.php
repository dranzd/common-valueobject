<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\ActorType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActorType::class)]
final class ActorTypeTest extends TestCase
{
    public function testCaseListAndValues(): void
    {
        $map = [];
        foreach (ActorType::cases() as $case) {
            $map[$case->name] = $case->value;
        }

        self::assertEquals(['User', 'System', 'Integration', 'Unknown'], array_keys($map));
        self::assertEquals(array_keys($map), array_values($map));
    }

    /**
     * @return array<string, array{string, string|null}>
     */
    public static function lookupProvider(): array
    {
        return [
            'exact' => ['User', 'User'],
            'wrong case' => ['user', null],
            'unknown' => ['Robot', null],
        ];
    }

    #[DataProvider('lookupProvider')]
    public function testTryFromIsCaseSensitive(string $input, ?string $expected): void
    {
        self::assertSame($expected, ActorType::tryFrom($input)?->value);
    }

    public function testFromRejectsWrongCaseWithAValueError(): void
    {
        $this->expectException(\ValueError::class);

        ActorType::from('user');
    }
}
