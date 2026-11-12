<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * A single search criterion: either a Filter (narrows results) or a Sorter (orders them).
 *
 * Concrete criteria are small immutable classes; they are built from a (code, value) pair,
 * typically taken from a request query string, via fromCodeAndValueOrNull().
 */
interface Criteria
{
    /**
     * Whether both criteria are of the same kind and carry the same value.
     */
    public function sameValueAs(Criteria $criteria): bool;

    public function __toString(): string;

    /**
     * Builds the criterion for the given code and raw value, or null when the code or value is not
     * understood by this class.
     */
    public static function fromCodeAndValueOrNull(string $code, mixed $value): ?Criteria;

    public function isForFiltering(): bool;

    public function isForSorting(): bool;
}
