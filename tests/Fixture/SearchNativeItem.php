<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

/**
 * Result item exposing only toNative().
 */
class SearchNativeItem
{
    public function __construct(private string $name)
    {
    }

    public function toNative(): string
    {
        return 'native-' . $this->name;
    }
}
