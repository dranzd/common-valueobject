<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * An array-translatable value object compared through the trait.
 */
final class CoreArrayValue implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    /**
     * @param array<string|int, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return 'array value';
    }

    public static function fromArray(array $array): ValueObject
    {
        return new self($array);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
