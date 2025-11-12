<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits;

/**
 * Trait for testing toNative() method of value objects.
 */
trait ToNativeTestTrait
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
     * Test that toNative() returns the correct native value.
     *
     * @dataProvider validNativeValuesProvider
     */
    public function testToNativeReturnsCorrectValue(mixed $value): void
    {
        $class = $this->getValueObjectClass();
        $valueObject = $class::fromNative($value);
        $native = $valueObject->toNative();

        // For scalar values, assert equality
        if (is_scalar($value)) {
            $this->assertEquals($value, $native);
        }
        // For arrays, assert structure matches
        elseif (is_array($value)) {
            $this->assertIsArray($native);
            $this->assertEquals($value, $native);
        }
        // For objects, convert to array or string
        else {
            $this->assertNotNull($native);
        }
    }

    /**
     * Test that toNative() returns consistent values.
     *
     * @dataProvider validNativeValuesProvider
     */
    public function testToNativeIsConsistent(mixed $value): void
    {
        $class = $this->getValueObjectClass();
        $valueObject = $class::fromNative($value);
        
        $native1 = $valueObject->toNative();
        $native2 = $valueObject->toNative();

        $this->assertEquals($native1, $native2, 'toNative() should return consistent values');
    }

    /**
     * Data provider for valid native values.
     *
     * @return array<array<mixed>>
     */
    public static function validNativeValuesProvider(): array
    {
        $instance = new static('testToNativeReturnsCorrectValue');
        $values = $instance->getValidNativeValues();
        
        return array_map(fn($value) => [$value], $values);
    }
}
