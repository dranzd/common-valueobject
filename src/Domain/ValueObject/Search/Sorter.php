<?php

namespace Dranzd\Common\Domain\ValueObject\Search;

interface Sorter extends Criteria
{
    /**
     * @return Sorter
     */
    public static function inAscendingOrder(): Sorter;

    /**
     * @return Sorter
     */
    public static function inDescendingOrder(): Sorter;

    /**
     * @return integer SORT_ASC|SORT_DESC
     */
    public function getOrder(): int;
}
