<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

/**
 * Result item exposing toArray() (and also toNative(), which must lose).
 */
class SearchArrayItem
{
    public function __construct(private string $name)
    {
    }

    /**
     * @return array{name: string}
     */
    public function toArray(): array
    {
        return ['name' => $this->name];
    }

    public function toNative(): string
    {
        return 'native-' . $this->name;
    }
}
