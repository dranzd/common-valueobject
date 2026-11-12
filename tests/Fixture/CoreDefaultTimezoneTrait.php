<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

/**
 * Runs a callback under another default timezone and always restores the previous one.
 */
trait CoreDefaultTimezoneTrait
{
    private static function withDefaultTimezone(string $zone, callable $callback): void
    {
        $previous = \date_default_timezone_get();
        \date_default_timezone_set($zone);

        try {
            $callback();
        } finally {
            \date_default_timezone_set($previous);
        }
    }

    /**
     * A zone whose current calendar date differs from the UTC date, so `date()` and `gmdate()` give different days.
     */
    private static function zoneOnAnotherDayThanUtc(): string
    {
        return (int) \gmdate('G') >= 12 ? 'Pacific/Kiritimati' : 'Etc/GMT+12';
    }
}
