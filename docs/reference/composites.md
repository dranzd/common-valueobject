# Composites: Quantity, File, Address

Three small immutable value objects built from several strings or numbers. All implement
`ArrayValueObjectTranslatorCapable` and share the same input conventions.

## Input conventions

`fromArray()` accepts:

- a **list** in the documented order, used whenever the array has an element `0`, even a null one. The values are
  taken in order, whatever their keys; surplus elements are ignored. Every part is cast (`(string)`, or `(float)` for a
  quantity value) and none is validated, so null becomes `''` (or `0.0`);
- an array with **keys**. Keys are converted to camelCase before matching, so `zip_code`, `zip-code`, `zipCode` and
  `ZipCode` are the same key, and `Label` works for `label`; `LABEL` does not (it becomes `lABEL`). When two keys
  convert to the same name the later one wins: `['zip_code' => '1000', 'zipCode' => '2000']` gives `2000`. Surplus keys
  are ignored. A key present with a null value counts as present and is cast.

Errors are `\InvalidArgumentException`:

- a list that is too short: `Quantity needs two arguments: value and unit.`, `File needs four arguments: label, name,
  type and path.` or `Address needs eight arguments: see Address::fromArray().`;
- a keyed array that lacks a key, naming the first missing key and the accepted ones, for example
  `Missing key "name"; accepted keys: label, name, type, path.`;
- a quantity value that is NAN or infinite: `Quantity value must be a finite number.`

Nothing else is rejected by the library. A part that cannot be cast is not reported as an `\InvalidArgumentException`:
an array in a string part becomes `Array` with a PHP warning, and an object without `__toString()` raises an `\Error`.

`sameValueAs()` is class-sensitive and compares `toArray()`; `__toString()` is the object-hash default.

## `Custom\Quantity\Basic`

A real number with a free-form unit. Designed to be extended; the constructor and `fromNative()` are `protected`.

```php
public static function fromArray(array $array): Basic;   // [value, unit] or ['value' =>, 'unit' =>]; returns static
public static function ofNoValue(): Basic;                // 0.0 and ''
public function toArray(): array;                         // ['value' => float, 'unit' => string]
final public function getValue(): Native\Number\Real;
final public function getUnit(): Native\String\Literal;
```

The value is cast with `(float)`, so fractions are kept: `[1.5, 'kg']` and `['1.5', 'kg']` both give `1.5`. A
non-numeric string such as `'abc'` casts to `0.0`.

```php
use Dranzd\Common\Domain\ValueObject\Custom\Quantity\Basic as Quantity;

echo json_encode(Quantity::fromArray([1.5, 'kg'])->toArray());   // {"value":1.5,"unit":"kg"}

try {
    Quantity::fromArray([NAN, 'kg']);
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();                                        // Quantity value must be a finite number.
}
```

## `File\File`

A reference to a stored file: `label`, `name`, `type` (for example a MIME type) and `path`.

```php
final public static function fromArray(array $array): File;   // [label, name, type, path] or the four keys
final public static function notProvided(): File;             // four empty strings
final public function toArray(): array;                       // ['label', 'name', 'type', 'path']
final public function getLabel(): Literal;   // also getName(), getType(), getPath()
```

The class is not final, but its constructor and `fromNative()` are private: `SubFile::fromArray()` works and returns a
plain `File`, never the subclass.

```php
use Dranzd\Common\Domain\ValueObject\File\File;

echo json_encode(File::fromArray([
    'label' => 'Avatar', 'name' => 'a.jpg', 'type' => 'image/jpeg', 'path' => '/up/a.jpg',
])->toArray());
// {"label":"Avatar","name":"a.jpg","type":"image\/jpeg","path":"\/up\/a.jpg"}
```

## `Location\Address`

A postal address. Final.

```php
final public static function fromArray(array $array): Address;
final public static function notProvided(): Address;          // eight empty strings
final public function toArray(): array;
// getCompany(), getAddressee(), getStreet1(), getStreet2(), getCity(), getProvince(), getCountry(), getZipCode()
```

List order: company, addressee, street1, street2, city, province, country, zip code. `toArray()` keys are
`company`, `addressee`, `street1`, `street2`, `city`, `province`, `country`, `zip_code`; on input, `zipCode` is
accepted as well (but not both together).

```php
use Dranzd\Common\Domain\ValueObject\Location\Address;

echo json_encode(Address::fromArray([
    'company' => 'Acme', 'addressee' => 'Jane', 'street1' => '1 Main', 'street2' => '',
    'city' => 'Manila', 'province' => 'NCR', 'country' => 'PH', 'zipCode' => '1000',
])->toArray());
// {"company":"Acme","addressee":"Jane","street1":"1 Main","street2":"","city":"Manila","province":"NCR","country":"PH","zip_code":"1000"}
```

With both `zip_code` and `zipCode` present, the later one wins:

```php
echo Address::fromArray([
    'company' => 'Acme', 'addressee' => 'Jane', 'street1' => '1 Main', 'street2' => '',
    'city' => 'Manila', 'province' => 'NCR', 'country' => 'PH', 'zip_code' => '1000', 'zipCode' => '2000',
])->getZipCode()->toNative();                                     // 2000
```
