<?php

// strict_types is omitted on purpose: calls made from this trait keep PHP's scalar coercion for the using class.

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * Supplies the Sorter half of the Criteria contract for a concrete sorter class.
 *
 * Contract for the using class:
 *  - it declares a property named `$order` holding SORT_ASC or SORT_DESC;
 *  - its constructor takes that order as its only required argument (the factories call
 *    `new static(SORT_ASC)` / `new static(SORT_DESC)`);
 *  - it implements __toString();
 *  - it defines a FILTER_CODE constant (the same name is used for sorters as for filters) if it is to be
 *    built by fromCodeAndValueOrNull().
 *
 * @property int $order
 */
trait SorterTrait
{
    public static function inAscendingOrder(): Sorter
    {
        $class = static::class;

        return new $class(SORT_ASC);
    }

    public static function inDescendingOrder(): Sorter
    {
        $class = static::class;

        return new $class(SORT_DESC);
    }

    /**
     * Builds the sorter when $code equals the class's FILTER_CODE constant and $value is exactly the
     * integer SORT_ASC (4) or SORT_DESC (3); otherwise returns null.
     *
     * The comparison is strict: a query-string value such as '4' does not match and the caller must
     * cast it to int first. A class without FILTER_CODE never matches.
     *
     * @param mixed $value
     */
    public static function fromCodeAndValueOrNull(string $code, mixed $value): ?Sorter
    {
        if (!\defined(static::class . '::FILTER_CODE')) {
            return null;
        }
        if ($code !== \constant(static::class . '::FILTER_CODE')) {
            return null;
        }
        if (SORT_ASC === $value) {
            return static::inAscendingOrder();
        }
        if (SORT_DESC === $value) {
            return static::inDescendingOrder();
        }
        return null;
    }

    public function sameValueAs(Criteria $object): bool
    {
        return $object instanceof static
            && $this->sameClassAs($object)
            && $this->order === $object->order
        ;
    }

    public function isForFiltering(): bool
    {
        return false;
    }

    public function isForSorting(): bool
    {
        return true;
    }

    /**
     * Whether the other object is an instance of exactly the same class as this one.
     */
    public function sameClassAs(object $object): bool
    {
        return get_class($this) === get_class($object);
    }

    /**
     * @return int SORT_ASC or SORT_DESC
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}
