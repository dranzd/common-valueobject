<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Search;

use Webmozart\Assert\Assert;

/**
 * Immutable page selector: a starting page number and a page size limit.
 *
 * The page number may not be zero. Negative numbers and limits are currently accepted. The meaning of
 * a limit of 0 (as used by startDefault()) is left to the consumer; this class does not interpret it.
 */
class Page
{
    private int $number;

    private int $limit;

    public function __construct(int $number, int $limit)
    {
        Assert::notEmpty($number, 'Page number must not be zero');

        $this->number = $number;
        $this->limit  = $limit;
    }

    /**
     * Page 1 with a limit of 0.
     */
    public static function startDefault(): Page
    {
        return new self(1, 0);
    }

    public function startFrom(int $number): Page
    {
        Assert::notEmpty($number, 'Page number must not be zero');

        $clone = clone $this;

        $clone->number = $number;

        return $clone;
    }

    public function limitTo(int $limit): Page
    {
        $clone = clone $this;

        $clone->limit = $limit;

        return $clone;
    }

    public function getStartingNumber(): int
    {
        return $this->number;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
