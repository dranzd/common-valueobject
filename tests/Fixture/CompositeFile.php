<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Fixture;

use Dranzd\Common\Domain\ValueObject\File\File;

/**
 * Subclass of File: File is not final, but its constructor and fromNative() are private.
 */
final class CompositeFile extends File
{
}
