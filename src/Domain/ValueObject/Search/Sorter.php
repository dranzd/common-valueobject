<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * A Criteria that orders a search in ascending or descending order; implementations usually use SorterTrait.
 */
interface Sorter extends Criteria
{
    public static function inAscendingOrder(): Sorter;

    public static function inDescendingOrder(): Sorter;

    /**
     * @return int SORT_ASC or SORT_DESC
     */
    public function getOrder(): int;
}
