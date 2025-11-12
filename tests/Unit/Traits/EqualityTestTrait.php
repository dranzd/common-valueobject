<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\Traits;

/**
 * Trait for testing equality (sameValueAs) of value objects.
 */
trait EqualityTestTrait
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
     * Test that two value objects with same value are equal.
     */
    public function testSameValueAsReturnsTrueForEqualValues(): void
    {
        $values = $this->getValidNativeValues();
        if (empty($values)) {
            $this->markTestSkipped('No valid values provided for equality testing');
        }

        $value = $values[0];
        $class = $this->getValueObjectClass();

        $valueObject1 = $class::fromNative($value);
        $valueObject2 = $class::fromNative($value);

        $this->assertTrue(
            $valueObject1->sameValueAs($valueObject2),
            'Value objects with same value should be equal'
        );
    }

    /**
     * Test that two value objects with different values are not equal.
     */
    public function testSameValueAsReturnsFalseForDifferentValues(): void
    {
        $values = $this->getValidNativeValues();
        if (count($values) < 2) {
            $this->markTestSkipped('Need at least 2 valid values for inequality testing');
        }

        $class = $this->getValueObjectClass();

        $valueObject1 = $class::fromNative($values[0]);
        $valueObject2 = $class::fromNative($values[1]);

        $this->assertFalse(
            $valueObject1->sameValueAs($valueObject2),
            'Value objects with different values should not be equal'
        );
    }

    /**
     * Test that equality is reflexive (a equals a).
     */
    public function testEqualityIsReflexive(): void
    {
        $values = $this->getValidNativeValues();
        if (empty($values)) {
            $this->markTestSkipped('No valid values provided for reflexive testing');
        }

        $class = $this->getValueObjectClass();
        $valueObject = $class::fromNative($values[0]);

        $this->assertTrue(
            $valueObject->sameValueAs($valueObject),
            'A value object should be equal to itself (reflexive)'
        );
    }

    /**
     * Test that equality is symmetric (if a equals b, then b equals a).
     */
    public function testEqualityIsSymmetric(): void
    {
        $values = $this->getValidNativeValues();
        if (empty($values)) {
            $this->markTestSkipped('No valid values provided for symmetric testing');
        }

        $value = $values[0];
        $class = $this->getValueObjectClass();

        $valueObject1 = $class::fromNative($value);
        $valueObject2 = $class::fromNative($value);

        $this->assertTrue(
            $valueObject1->sameValueAs($valueObject2),
            'a should equal b'
        );
        $this->assertTrue(
            $valueObject2->sameValueAs($valueObject1),
            'b should equal a (symmetric)'
        );
    }

    /**
     * Test that equality is transitive (if a equals b and b equals c, then a equals c).
     */
    public function testEqualityIsTransitive(): void
    {
        $values = $this->getValidNativeValues();
        if (empty($values)) {
            $this->markTestSkipped('No valid values provided for transitive testing');
        }

        $value = $values[0];
        $class = $this->getValueObjectClass();

        $valueObject1 = $class::fromNative($value);
        $valueObject2 = $class::fromNative($value);
        $valueObject3 = $class::fromNative($value);

        $this->assertTrue($valueObject1->sameValueAs($valueObject2), 'a should equal b');
        $this->assertTrue($valueObject2->sameValueAs($valueObject3), 'b should equal c');
        $this->assertTrue(
            $valueObject1->sameValueAs($valueObject3),
            'a should equal c (transitive)'
        );
    }
}
