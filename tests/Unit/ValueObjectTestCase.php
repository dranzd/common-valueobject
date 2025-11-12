<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for value object tests.
 * 
 * Provides common assertion methods and utilities for testing value objects.
 */
abstract class ValueObjectTestCase extends TestCase
{
    /**
     * Assert that two value objects are equal.
     *
     * @param ValueObject $expected
     * @param ValueObject $actual
     * @param string $message
     */
    protected function assertValueObjectEquals(
        ValueObject $expected,
        ValueObject $actual,
        string $message = ''
    ): void {
        $this->assertTrue(
            $expected->sameValueAs($actual),
            $message ?: sprintf(
                'Failed asserting that %s equals %s',
                $this->valueObjectToString($expected),
                $this->valueObjectToString($actual)
            )
        );
    }

    /**
     * Assert that two value objects are not equal.
     *
     * @param ValueObject $expected
     * @param ValueObject $actual
     * @param string $message
     */
    protected function assertValueObjectNotEquals(
        ValueObject $expected,
        ValueObject $actual,
        string $message = ''
    ): void {
        $this->assertFalse(
            $expected->sameValueAs($actual),
            $message ?: sprintf(
                'Failed asserting that %s does not equal %s',
                $this->valueObjectToString($expected),
                $this->valueObjectToString($actual)
            )
        );
    }

    /**
     * Assert that a value object is immutable (has no public setters).
     *
     * @param ValueObject $valueObject
     * @param string $message
     */
    protected function assertImmutable(ValueObject $valueObject, string $message = ''): void
    {
        $reflection = new \ReflectionClass($valueObject);
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($publicMethods as $method) {
            $methodName = $method->getName();
            
            // Check for setter methods (starts with 'set')
            if (str_starts_with($methodName, 'set')) {
                $this->fail(
                    $message ?: sprintf(
                        'Value object %s has a public setter method: %s(). Value objects must be immutable.',
                        $reflection->getName(),
                        $methodName
                    )
                );
            }
        }

        // Check for public non-readonly properties
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            if (!$property->isReadOnly()) {
                $this->fail(
                    $message ?: sprintf(
                        'Value object %s has a public non-readonly property: $%s. Value objects must be immutable.',
                        $reflection->getName(),
                        $property->getName()
                    )
                );
            }
        }

        $this->assertTrue(true); // If we get here, the object is immutable
    }

    /**
     * Assert that validation fails with the expected exception.
     *
     * @param string $exceptionClass
     * @param callable $callback
     * @param string $message
     */
    protected function assertValidationFails(
        string $exceptionClass,
        callable $callback,
        string $message = ''
    ): void {
        $this->expectException($exceptionClass);
        if ($message) {
            $this->expectExceptionMessage($message);
        }
        $callback();
    }

    /**
     * Convert a value object to a string representation for error messages.
     *
     * @param ValueObject $valueObject
     * @return string
     */
    protected function valueObjectToString(ValueObject $valueObject): string
    {
        try {
            $native = $valueObject->toNative();
            if (is_scalar($native)) {
                return (string) $native;
            }
            return json_encode($native, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return get_class($valueObject);
        }
    }

    /**
     * Create a value object from native value for testing.
     *
     * @param string $class
     * @param mixed $value
     * @return ValueObject
     */
    protected function createValueObject(string $class, mixed $value): ValueObject
    {
        if (!is_subclass_of($class, ValueObject::class)) {
            throw new \InvalidArgumentException(
                sprintf('%s must implement %s', $class, ValueObject::class)
            );
        }

        return $class::fromNative($value);
    }

    /**
     * Assert that a value object can be serialized and deserialized correctly.
     *
     * @param ValueObject $valueObject
     * @param string $message
     */
    protected function assertSerializable(ValueObject $valueObject, string $message = ''): void
    {
        $native = $valueObject->toNative();
        $class = get_class($valueObject);
        $reconstructed = $class::fromNative($native);

        $this->assertValueObjectEquals(
            $valueObject,
            $reconstructed,
            $message ?: sprintf(
                'Failed asserting that %s can be serialized and deserialized correctly',
                $class
            )
        );
    }

    /**
     * Assert that a value object can be JSON encoded and decoded correctly.
     *
     * @param ValueObject $valueObject
     * @param string $message
     */
    protected function assertJsonSerializable(ValueObject $valueObject, string $message = ''): void
    {
        $native = $valueObject->toNative();
        $json = json_encode($native, JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        
        $class = get_class($valueObject);
        $reconstructed = $class::fromNative($decoded);

        $this->assertValueObjectEquals(
            $valueObject,
            $reconstructed,
            $message ?: sprintf(
                'Failed asserting that %s can be JSON serialized and deserialized correctly',
                $class
            )
        );
    }
}
