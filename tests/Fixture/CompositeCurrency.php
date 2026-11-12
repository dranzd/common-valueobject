<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Money\Currency;

/**
 * Subclass of Currency: every factory must return the subclass.
 */
final class CompositeCurrency extends Currency
{
}
