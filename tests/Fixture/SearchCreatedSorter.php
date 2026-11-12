<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

/**
 * A second sorter with a different code, to exercise provider look-ups.
 */
class SearchCreatedSorter extends SearchNameSorter
{
    public const FILTER_CODE = 'created';
}
