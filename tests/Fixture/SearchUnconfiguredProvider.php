<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Search\CriteriaProviderTrait;

/**
 * Provider that never assigns its lists (they stay null).
 */
class SearchUnconfiguredProvider
{
    use CriteriaProviderTrait;
}
