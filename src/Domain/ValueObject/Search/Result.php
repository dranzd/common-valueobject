<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * Immutable search result: the items found for one page plus the total number of matches.
 */
class Result
{
    /**
     * @var array<int|string, mixed>
     */
    private array $items;

    private int $total;

    /**
     * @param array<int|string, mixed> $items
     */
    public function __construct(array $items, int $total)
    {
        $this->items = $items;
        $this->total = $total;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Items are converted one level deep: an object with toArray() (preferred) or toNative() is replaced by
     * that method's result; everything else (scalars, arrays, other objects) is left as it is.
     *
     * @return array{items: array<int|string, mixed>, total: int}
     */
    public function toArray(): array
    {
        $items = array_map(
            function ($item) {
                if (is_object($item)) {
                    if (method_exists($item, 'toArray')) {
                        return $item->toArray();
                    }
                    if (method_exists($item, 'toNative')) {
                        return $item->toNative();
                    }
                }
                return $item;
            },
            $this->items
        );

        return [
            'items' => $items,
            'total' => $this->total,
        ];
    }
}
