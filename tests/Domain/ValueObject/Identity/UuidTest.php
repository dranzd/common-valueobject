<?php

namespace Dranzd\Common\Domain\ValueObject\Tests\Domain\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testCanCreateUuidWithoutValue(): void
    {
        $uuid = new Uuid();

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid->toNative()
        );
    }

    public function testCanCreateUuidFromString(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $uuid = Uuid::fromNative($uuidString);

        $this->assertSame($uuidString, $uuid->toNative());
    }

    public function testGenerateAsStringReturnsValidUuid(): void
    {
        $uuidString = Uuid::generateAsString();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuidString
        );
    }

    public function testThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Uuid::fromNative('not-a-valid-uuid');
    }

    public function testTwoUuidsWithSameValueAreEqual(): void
    {
        $uuid1 = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');
        $uuid2 = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');

        $this->assertTrue($uuid1->sameValueAs($uuid2));
    }

    public function testTwoUuidsWithDifferentValueAreNotEqual(): void
    {
        $uuid1 = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');
        $uuid2 = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440001');

        $this->assertFalse($uuid1->sameValueAs($uuid2));
    }
}
