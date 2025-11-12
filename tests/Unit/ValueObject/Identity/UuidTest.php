<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObjectTestCase;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\FromNativeTestTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\ToNativeTestTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\EqualityTestTrait;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits\SerializationTestTrait;

/**
 * Test case for UUID value object.
 */
final class UuidTest extends ValueObjectTestCase
{
    use FromNativeTestTrait;
    use ToNativeTestTrait;
    use EqualityTestTrait;
    use SerializationTestTrait;

    protected function getValueObjectClass(): string
    {
        return Uuid::class;
    }

    protected function getValidNativeValues(): array
    {
        return [
            '550e8400-e29b-41d4-a716-446655440000',
            '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
            '6ba7b811-9dad-11d1-80b4-00c04fd430c8',
        ];
    }

    protected function getInvalidNativeValues(): array
    {
        return [
            'not-a-uuid',
            '123',
            '',
            'invalid-uuid-format',
            123,
            null,
        ];
    }

    public function testGenerate(): void
    {
        $uuid = Uuid::generate();

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid->toNative()
        );
    }

    public function testGenerateCreatesUniqueValues(): void
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::generate();

        $this->assertValueObjectNotEquals($uuid1, $uuid2);
    }

    public function testToString(): void
    {
        $value = '550e8400-e29b-41d4-a716-446655440000';
        $uuid = Uuid::fromNative($value);

        $this->assertEquals($value, (string) $uuid);
    }

    public function testImmutability(): void
    {
        $uuid = Uuid::generate();
        $this->assertImmutable($uuid);
    }
}
