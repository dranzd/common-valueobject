<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\File;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Label;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Name;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Path;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Type;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Reference to a stored file: a label, a file name, a type (e.g. a MIME type) and a path. All four parts are
 * free-form strings and are not validated.
 *
 * Instances are immutable. The constructor and `fromNative()` are private, so a subclass cannot customise
 * construction: `Sub::fromArray()` works but returns a plain `File`.
 */
class File implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;
    use ToStringUsingObjectHashTrait;

    private Label $label;

    private Name $name;

    private Type $type;

    private Path $path;

    private function __construct(
        Label $label,
        Name $name,
        Type $type,
        Path $path
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->path = $path;
    }

    /**
     * A file whose four parts are empty strings.
     */
    final public static function notProvided(): File
    {
        return self::fromArray(['', '', '', '']);
    }

    /**
     * Build from a positional list `[label, name, type, path]` or from an array with those keys.
     *
     * Every part is cast with `(string)`. Surplus elements and keys are ignored. The list form is used whenever
     * the array has an element `0`, even a null one; its values are taken in order, whatever their keys. Keys are
     * matched after camelCase conversion, so `Label` works too; when two keys convert to the same name the later one
     * wins.
     *
     * @param array<array-key, mixed> $array
     * @throws \InvalidArgumentException If an array with keys lacks one of the four keys, or if a list has fewer than
     *                                   four elements.
     */
    final public static function fromArray(array $array): File
    {
        if (\array_key_exists(0, $array)) {
            return self::fromNative(...\array_values($array));
        }

        $data = ArrayKeys::toCamelCase($array);
        foreach (['label', 'name', 'type', 'path'] as $key) {
            if (!\array_key_exists($key, $data)) {
                throw new \InvalidArgumentException(
                    \sprintf('Missing key "%s"; accepted keys: label, name, type, path.', $key)
                );
            }
        }

        return self::fromNative($data['label'], $data['name'], $data['type'], $data['path']);
    }

    final public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    /**
     * @return array{label: string, name: string, type: string, path: string}
     */
    final public function toArray(): array
    {
        return [
            'label' => $this->label->toNative(),
            'name' => $this->name->toNative(),
            'type' => $this->type->toNative(),
            'path' => $this->path->toNative(),
        ];
    }

    final public function getLabel(): Label
    {
        return $this->label;
    }

    final public function getName(): Name
    {
        return $this->name;
    }

    final public function getType(): Type
    {
        return $this->type;
    }

    final public function getPath(): Path
    {
        return $this->path;
    }

    private static function fromNative(): File
    {
        if (\func_num_args() < 4) {
            throw new \InvalidArgumentException('File needs four arguments: label, name, type and path.');
        }

        $label = (string) \func_get_arg(0);
        $name = (string) \func_get_arg(1);
        $type = (string) \func_get_arg(2);
        $path = (string) \func_get_arg(3);

        $label = Label::fromNative($label);
        $name = Name::fromNative($name);
        $type = Type::fromNative($type);
        $path = Path::fromNative($path);

        return new self(
            $label,
            $name,
            $type,
            $path
        );
    }
}
