<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\File;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\File\File;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\Tests\Fixture\CompositeFile;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(File::class)]
#[UsesClass(ArrayKeys::class)]
#[UsesClass(Literal::class)]
#[UsesClass(CompositeFile::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesTrait(ToStringUsingObjectHashTrait::class)]
final class FileTest extends TestCase
{
    private const NAMED = ['label' => 'Avatar', 'name' => 'a.jpg', 'type' => 'image/jpeg', 'path' => '/up/a.jpg'];

    public function testFromArrayPositional(): void
    {
        $file = File::fromArray(['Avatar', 'a.jpg', 'image/jpeg', '/up/a.jpg']);

        self::assertSame(self::NAMED, $file->toArray());
        self::assertSame('Avatar', $file->getLabel()->toNative());
        self::assertSame('a.jpg', $file->getName()->toNative());
        self::assertSame('image/jpeg', $file->getType()->toNative());
        self::assertSame('/up/a.jpg', $file->getPath()->toNative());
    }

    public function testFromArrayPositionalIgnoresSurplusElements(): void
    {
        $file = File::fromArray(['Avatar', 'a.jpg', 'image/jpeg', '/up/a.jpg', 'ignored']);

        self::assertSame(self::NAMED, $file->toArray());
    }

    public function testFromArrayNamed(): void
    {
        self::assertSame(self::NAMED, File::fromArray(self::NAMED)->toArray());
    }

    public function testFromArrayNamedIgnoresSurplusKeys(): void
    {
        self::assertSame(self::NAMED, File::fromArray(self::NAMED + ['size' => 12])->toArray());
    }

    public function testFromArrayCastsPartsToString(): void
    {
        $file = File::fromArray([1, 2, 3, 4]);

        self::assertSame(['label' => '1', 'name' => '2', 'type' => '3', 'path' => '4'], $file->toArray());
    }

    public function testNotProvided(): void
    {
        self::assertSame(['label' => '', 'name' => '', 'type' => '', 'path' => ''], File::notProvided()->toArray());
    }

    /**
     * @return array<string, array{list<string>}>
     */
    public static function tooShortListProvider(): array
    {
        return [
            'three elements, one short' => [['a', 'b', 'c']],
            'two elements' => [['a', 'b']],
            'one element' => [['a']],
        ];
    }

    /**
     * @param list<string> $input
     */
    #[DataProvider('tooShortListProvider')]
    public function testTooShortListNamesTheArity(array $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File needs four arguments: label, name, type and path.');

        File::fromArray($input);
    }

    public function testListOfExactlyFourElementsIsEnough(): void
    {
        self::assertSame(self::NAMED, File::fromArray(\array_values(self::NAMED))->toArray());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function missingKeyProvider(): array
    {
        return [
            'label' => ['label'],
            'name' => ['name'],
            'type' => ['type'],
            'path' => ['path'],
        ];
    }

    #[DataProvider('missingKeyProvider')]
    public function testEveryMissingKeyIsNamed(string $missing): void
    {
        $input = self::NAMED;
        unset($input[$missing]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf('Missing key "%s"; accepted keys: label, name, type, path.', $missing)
        );

        File::fromArray($input);
    }

    public function testEmptyArrayNamesTheFirstKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "label"');

        File::fromArray([]);
    }

    public function testListWhoseFirstElementIsNullIsStillAList(): void
    {
        // Pinned for compatibility: a null first element does not switch to the keyed form; it casts to ''.
        $file = File::fromArray([null, 'a.jpg', 'image/jpeg', '/up/a.jpg']);

        self::assertSame(\array_replace(self::NAMED, ['label' => '']), $file->toArray());
    }

    public function testListIgnoresNonSequentialAndStringKeys(): void
    {
        self::assertSame(
            self::NAMED,
            File::fromArray([0 => 'Avatar', 5 => 'a.jpg', 6 => 'image/jpeg', 'x' => '/up/a.jpg'])->toArray()
        );
    }

    public function testPascalCaseKeysAreAccepted(): void
    {
        $file = File::fromArray(
            ['Label' => 'Avatar', 'Name' => 'a.jpg', 'Type' => 'image/jpeg', 'Path' => '/up/a.jpg']
        );

        self::assertSame(self::NAMED, $file->toArray());
    }

    public function testAllCapsKeysAreNotMatched(): void
    {
        // Pinned for compatibility: 'NAME' converts to 'nAME', which is not 'name'.
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "name"');

        File::fromArray(['label' => 'l', 'NAME' => 'n', 'type' => 't', 'path' => 'p']);
    }

    public function testCollidingKeysLetTheLaterOneWin(): void
    {
        // Pinned for compatibility.
        $file = File::fromArray(self::NAMED + ['Path' => '/later']);

        self::assertSame('/later', $file->getPath()->toNative());
    }

    public function testNullPartsBecomeEmptyStrings(): void
    {
        $file = File::fromArray(['label' => null, 'name' => 'n', 'type' => 't', 'path' => null]);

        self::assertSame(['label' => '', 'name' => 'n', 'type' => 't', 'path' => ''], $file->toArray());
    }

    public function testSubclassFromArrayReturnsPlainFile(): void
    {
        $positional = CompositeFile::fromArray(['Avatar', 'a.jpg', 'image/jpeg', '/up/a.jpg']);
        $named = CompositeFile::fromArray(self::NAMED);

        self::assertSame(File::class, $positional::class);
        self::assertSame(File::class, $named::class);
        self::assertSame(File::class, CompositeFile::notProvided()::class);
        self::assertSame(self::NAMED, $positional->toArray());
    }

    public function testSameValueAs(): void
    {
        $file = File::fromArray(self::NAMED);

        self::assertTrue($file->sameValueAs(File::fromArray(self::NAMED)));
        self::assertFalse($file->sameValueAs(File::fromArray(['path' => '/other'] + self::NAMED)));
        self::assertFalse($file->sameValueAs(File::notProvided()));
    }

    public function testToStringUsesObjectHash(): void
    {
        $file = File::notProvided();

        self::assertStringContainsString(spl_object_hash($file), (string) $file);
    }
}
