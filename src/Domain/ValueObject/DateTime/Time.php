<?php

namespace Dranzd\Common\Domain\ValueObject\DateTime;

use Dranzd\Common\Domain\Assert\Assertion;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;

class Time extends Literal
{
    private const DATE_FORMAT = 'H:i:s';

    public function __construct($value = null)
    {
        if (null === $value) {
            $value = \date(self::DATE_FORMAT);
        }

        Assertion::string($value);
        self::isFormatValid($value);

        $this->value = $value;
    }

    final public static function isFormatValid($datetime): bool
    {
        return false !== \DateTime::createFromFormat(self::DATE_FORMAT, $datetime);
    }
}
