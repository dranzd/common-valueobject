<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Collection\Collection;

/**
 * A consumer collection written without any generic annotation; PHPStan must accept it at level 8.
 */
class CoreSources extends Collection
{
}
