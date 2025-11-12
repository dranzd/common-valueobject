<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits;

/**
 * Trait for testing serialization of value objects.
 */
trait SerializationTestTrait
{
    /**
     * Get the value object class to test.
     *
     * @return string
     */
    abstract protected function getValueObjectClass(): string;

    /**
     * Get array of valid native values for testing.
     *
     * @return array<mixed>
     */
    abstract protected function getValidNativeValues(): array;

    /**
     * Test that value object can be serialized and deserialized.
     *
     * @dataProvider validNativeValuesProvider
     */
    public function testSerializationRoundTrip(mixed $value): void
    {
        $class = $this->getValueObjectClass();
        $original = $class::fromNative($value);

        $native = $original->toNative();
        $reconstructed = $class::fromNative($native);

        $this->assertTrue(
            $original->sameValueAs($reconstructed),
            'Value object should be equal after serialization round trip'
        );
    }

    /**
     * Test that value object can be JSON encoded and decoded.
     *
     * @dataProvider validNativeValuesProvider
     */
    public function testJsonSerialization(mixed $value): void
    {
        $class = $this->getValueObjectClass();
        $original = $class::fromNative($value);

        $native = $original->toNative();
        $json = json_encode($native, JSON_THROW_ON_ERROR);
        $this->assertIsString($json);

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $reconstructed = $class::fromNative($decoded);

        $this->assertTrue(
            $original->sameValueAs($reconstructed),
            'Value object should be equal after JSON serialization round trip'
        );
    }

    /**
     * Data provider for valid native values.
     *
     * @return array<array<mixed>>
     */
    public static function validNativeValuesProvider(): array
    {
        $instance = new static('testSerializationRoundTrip');
        $values = $instance->getValidNativeValues();

        return array_map(fn($value) => [$value], $values);
    }
}
