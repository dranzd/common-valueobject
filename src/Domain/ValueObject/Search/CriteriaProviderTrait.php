<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

/**
 * Builds Filter and Sorter instances from (code, value) pairs, for example a request's query string.
 *
 * The using class assigns `$filters` and `$sorters` (usually in its constructor) with the class names of
 * its Filter and Sorter implementations. Each is asked in turn and the first non-null result wins.
 * A registered class that answers with something that is not a Filter / Sorter is skipped.
 * Lists that were never assigned are treated as empty. Assign the lists in the constructor and do not redeclare
 * the properties: they are untyped and have no default on purpose, and a redeclaration with any type, default or
 * visibility other than an identical `private $filters;` is a fatal error.
 */
trait CriteriaProviderTrait
{
    /**
     * @var list<class-string<Filter>>|null
     */
    private $filters;

    /**
     * @var list<class-string<Sorter>>|null
     */
    private $sorters;

    /**
     * First filter any registered class can build for the pair, or null when none can.
     */
    final public function filterFromCodeAndValueOrNull(string $code, mixed $value): ?Filter
    {
        foreach ($this->filters ?? [] as $filterClass) {
            $filter = $filterClass::fromCodeAndValueOrNull($code, $value);
            if ($filter instanceof Filter) {
                return $filter;
            }
        }

        return null;
    }

    /**
     * First sorter any registered class can build for the pair, or null when none can.
     */
    final public function sorterFromCodeAndValueOrNull(string $code, mixed $value): ?Sorter
    {
        foreach ($this->sorters ?? [] as $sorterClass) {
            $sorter = $sorterClass::fromCodeAndValueOrNull($code, $value);
            if ($sorter instanceof Sorter) {
                return $sorter;
            }
        }

        return null;
    }
}
