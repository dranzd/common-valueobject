<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits;

use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Trait for testing fromNative() method of value objects.
 */
trait FromNativeTestTrait
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
     * Get array of invalid native values for testing.
     *
     * @return array<mixed>
     */
    abstract protected function getInvalidNativeValues(): array;

    /**
     * Test that fromNative() creates value object with valid values.
     *
     * @dataProvider validNativeValuesProvider
     */
    public function testFromNativeWithValidValues(mixed $value): void
    {
        $class = $this->getValueObjectClass();
        $valueObject = $class::fromNative($value);

        $this->assertInstanceOf($class, $valueObject);
        $this->assertInstanceOf(ValueObject::class, $valueObject);
    }

    /**
     * Test that fromNative() throws exception with invalid values.
     *
     * @dataProvider invalidNativeValuesProvider
     */
    public function testFromNativeWithInvalidValues(mixed $value): void
    {
        $this->expectException(\Throwable::class);
        
        $class = $this->getValueObjectClass();
        $class::fromNative($value);
    }

    /**
     * Data provider for valid native values.
     *
     * @return array<array<mixed>>
     */
    public static function validNativeValuesProvider(): array
    {
        $instance = new static('testFromNativeWithValidValues');
        $values = $instance->getValidNativeValues();
        
        return array_map(fn($value) => [$value], $values);
    }

    /**
     * Data provider for invalid native values.
     *
     * @return array<array<mixed>>
     */
    public static function invalidNativeValuesProvider(): array
    {
        $instance = new static('testFromNativeWithInvalidValues');
        $values = $instance->getInvalidNativeValues();
        
        return array_map(fn($value) => [$value], $values);
    }
}
