<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Collection;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * An array-backed collection of items that is also a value object.
 *
 * Equality (`sameValueAs()`) compares `toArray()` strictly, so item order and types matter. `toArray()` converts
 * items: an object item is replaced by its `toArray()` or else its `toNative()` result when it has one of those
 * methods, and kept as is otherwise. Nested collections therefore convert recursively; objects inside plain
 * arrays are left as they are.
 *
 * Unlike the other value objects, this class inherits Doctrine's mutators (`add()`, `set()`, `remove()`,
 * `clear()` and so on), so a collection is not immutable.
 *
 * The templates have defaults so that a subclass written without generic annotations stays valid.
 *
 * @template TKey of array-key = array-key
 * @template T = mixed
 * @extends ArrayCollection<TKey, T>
 */
class Collection extends ArrayCollection implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return \sprintf('Object %s with object hash of %s', __METHOD__, \spl_object_hash($this));
    }

    /**
     * Wraps the array in a plain `Collection`.
     *
     * This always builds the base class, whichever class it is called on: a subclass with its own constructor
     * arguments, validation or visibility is never instantiated here. Subclasses should use their own constructor
     * or Doctrine's `createFrom()`.
     *
     * @param array<TKey, T> $array
     *
     * @return Collection<TKey, T>
     */
    public static function fromArray(array $array): Collection
    {
        return new Collection($array);
    }

    /**
     * @return array<TKey, mixed>
     */
    public function toArray(): array
    {
        return \array_map(
            function ($element) {
                if (!\is_object($element)) {
                    return $element;
                }
                if (\method_exists($element, 'toArray')) {
                    return $element->toArray();
                }
                if (\method_exists($element, 'toNative')) {
                    return $element->toNative();
                }
                return $element;
            },
            parent::toArray()
        );
    }

    /**
     * Tells whether the collection holds a value object with the same value as the given one.
     *
     * Returns false when the needle is not a value object.
     *
     * @param mixed $anItem
     */
    public function hasValueObjectItem($anItem): bool
    {
        $filteredItems = $this->filter(
            function ($item) use ($anItem) {
                return $item instanceof ValueObject && $anItem instanceof ValueObject && $item->sameValueAs($anItem);
            }
        );

        return !$filteredItems->isEmpty();
    }
}
