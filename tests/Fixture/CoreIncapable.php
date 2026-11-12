<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Exception\FromNativeValueIncapable;
use Dranzd\Common\Domain\ValueObject\Exception\ToNativeValueIncapable;
use Dranzd\Common\Domain\ValueObject\ScalarValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * A value object that cannot be translated to a native value.
 */
final class CoreIncapable implements ValueObject, ScalarValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    public function __toString(): string
    {
        return 'incapable';
    }

    public static function fromNative(): self
    {
        throw FromNativeValueIncapable::fromObject(new self());
    }

    public function toNative()
    {
        throw ToNativeValueIncapable::fromObject($this);
    }
}
