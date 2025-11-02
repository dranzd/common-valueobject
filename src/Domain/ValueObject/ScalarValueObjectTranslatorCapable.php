<?php

namespace Dranzd\Common\Domain\ValueObject;

interface ScalarValueObjectTranslatorCapable
{
    /**
     * @throws Exception\FromNativeValueIncapable
     */
    public static function fromNative(): ScalarValueObjectTranslatorCapable;

    /**
     * @throws Exception\ToNativeValueIncapable
     */
    public function toNative();
}
