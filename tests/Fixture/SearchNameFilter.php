<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\Search\Criteria;
use Dranzd\Common\Domain\ValueObject\Search\Filter;
use Dranzd\Common\Domain\ValueObject\Search\FilterTrait;

/**
 * Consumer-style filter: matches records whose name equals the given value.
 */
class SearchNameFilter implements Filter
{
    use FilterTrait;

    public const FILTER_CODE = 'name';

    public function __construct(protected string $name)
    {
    }

    public static function fromCodeAndValueOrNull(string $code, mixed $value): ?Criteria
    {
        if ($code !== self::FILTER_CODE || !is_string($value)) {
            return null;
        }

        return new self($value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function sameValueAs(Criteria $criteria): bool
    {
        return $criteria instanceof self && $this->sameClassAs($criteria) && $this->name === $criteria->name;
    }

    public function __toString(): string
    {
        return self::FILTER_CODE . '=' . $this->name;
    }
}
