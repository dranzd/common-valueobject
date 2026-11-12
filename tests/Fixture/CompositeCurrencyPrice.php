<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Money\Basic;
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;

/**
 * Money\Basic subclass whose currency is a {@see CompositeCurrency}, a Currency subclass holding the same code.
 */
final class CompositeCurrencyPrice extends Basic
{
    public static function of(int $amount, string $code): self
    {
        return new self(Integer::fromNative($amount), CompositeCurrency::fromString($code));
    }
}
