# Built-in Value Objects

This library provides a comprehensive set of ready-to-use value objects for common domain concepts.

## Native Types

### Boolean

Represents a boolean value.

```php
use Dranzd\Common\Domain\ValueObject\Native\Logic\Boolean;

$isActive = Boolean::fromNative(true);
echo $isActive->toNative(); // true

$isActive = Boolean::fromNative(false);
echo $isActive->toNative(); // false
```

### Integer

Represents an integer value.

```php
use Dranzd\Common\Domain\ValueObject\Native\Number\Integer;

$age = Integer::fromNative(25);
echo $age->toNative(); // 25
```

### Real

Represents a floating-point number.

```php
use Dranzd\Common\Domain\ValueObject\Native\Number\Real;

$price = Real::fromNative(19.99);
echo $price->toNative(); // 19.99
```

### Literal (String)

Represents a string value.

```php
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;

$name = Literal::fromNative('John Doe');
echo $name->toNative(); // "John Doe"
```

---

## Identity

### Uuid

Represents a universally unique identifier (UUID).

```php
use Dranzd\Common\Domain\ValueObject\Identity\Uuid;

// Generate new UUID
$id = new Uuid();
echo $id->toNative(); // "550e8400-e29b-41d4-a716-446655440000"

// From existing UUID string
$id = Uuid::fromNative('550e8400-e29b-41d4-a716-446655440000');

// Generate as string
$uuidString = Uuid::generateAsString();
```

**Features:**
- Automatic UUID v4 generation
- Validation of UUID format
- Uses `ramsey/uuid` internally

---

## DateTime

### Date

Represents a date without time.

```php
use Dranzd\Common\Domain\ValueObject\DateTime\Date;

// Current date
$today = new Date();

// Specific date
$date = Date::fromNative('2025-11-02');
echo $date->toNative(); // "2025-11-02"
```

**Format:** `Y-m-d` (e.g., "2025-11-02")

### DateTime

Represents a date and time.

```php
use Dranzd\Common\Domain\ValueObject\DateTime\DateTime;

// Current datetime
$now = new DateTime();

// Specific datetime
$datetime = DateTime::fromNative('2025-11-02 14:30:00');
echo $datetime->toNative(); // "2025-11-02 14:30:00"
```

**Format:** `Y-m-d H:i:s` (e.g., "2025-11-02 14:30:00")

### Time

Represents a time without date.

```php
use Dranzd\Common\Domain\ValueObject\DateTime\Time;

// Current time
$now = new Time();

// Specific time
$time = Time::fromNative('14:30:00');
echo $time->toNative(); // "14:30:00"
```

**Format:** `H:i:s` (e.g., "14:30:00")

---

## Money

### Basic Money

Represents monetary values with amount and currency.

```php
use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;

// From array
$price = Money::fromNative([
    'amount' => 1999,
    'currency' => 'USD'
]);

echo $price->getAmount();   // 1999
echo $price->getCurrency(); // "USD"

// To array
$array = $price->toArray();
// ['amount' => 1999, 'currency' => 'USD']
```

**Properties:**
- `amount` (integer) - Amount in smallest currency unit (e.g., cents)
- `currency` (string) - ISO 4217 currency code

**Usage Tips:**
- Store amounts in smallest unit (cents, not dollars)
- Use `MoneyUtil` for conversions between float and integer

---

## Quantity

### Basic Quantity

Represents a quantity with value and unit.

```php
use Dranzd\Common\Domain\ValueObject\Custom\Quantity\Basic as Quantity;

// From array
$weight = Quantity::fromNative([
    'value' => 5,
    'unit' => 'kg'
]);

echo $weight->getValue(); // 5
echo $weight->getUnit();  // "kg"

// To array
$array = $weight->toArray();
// ['value' => 5, 'unit' => 'kg']
```

**Properties:**
- `value` (integer) - Numeric value
- `unit` (string) - Unit of measurement

---

## Location

### Address

Represents a physical address.

```php
use Dranzd\Common\Domain\ValueObject\Location\Address;

$address = Address::fromNative([
    'company' => 'Acme Corp',
    'name' => 'John Doe',
    'line1' => '123 Main St',
    'line2' => 'Apt 4B',
    'city' => 'New York',
    'state' => 'NY',
    'zipCode' => '10001',
    'country' => 'USA'
]);

echo $address->getCompany();  // "Acme Corp"
echo $address->getName();     // "John Doe"
echo $address->getLine1();    // "123 Main St"
echo $address->getLine2();    // "Apt 4B"
echo $address->getCity();     // "New York"
echo $address->getState();    // "NY"
echo $address->getZipCode();  // "10001"
echo $address->getCountry();  // "USA"

// To array
$array = $address->toArray();
```

**Properties:**
- `company` (string, optional)
- `name` (string)
- `line1` (string)
- `line2` (string, optional)
- `city` (string)
- `state` (string)
- `zipCode` (string)
- `country` (string)

---

## File

### File

Represents a file with metadata.

```php
use Dranzd\Common\Domain\ValueObject\File\File;

$file = File::fromNative([
    'label' => 'Profile Picture',
    'name' => 'avatar.jpg',
    'path' => '/uploads/avatars/avatar.jpg',
    'type' => 'image/jpeg',
    'size' => 102400
]);

echo $file->getLabel(); // "Profile Picture"
echo $file->getName();  // "avatar.jpg"
echo $file->getPath();  // "/uploads/avatars/avatar.jpg"
echo $file->getType();  // "image/jpeg"
echo $file->getSize();  // 102400

// To array
$array = $file->toArray();
```

**Properties:**
- `label` (string) - Display label
- `name` (string) - File name
- `path` (string) - File path
- `type` (string) - MIME type
- `size` (integer) - File size in bytes

---

## Collection

### Collection

Generic collection value object.

```php
use Dranzd\Common\Domain\ValueObject\Collection\Collection;

$items = Collection::fromNative([1, 2, 3, 4, 5]);

echo $items->count(); // 5

// To array
$array = $items->toArray(); // [1, 2, 3, 4, 5]
```

---

## Search Components

### Page

Represents pagination information.

```php
use Dranzd\Common\Domain\ValueObject\Search\Page;

// Create page
$page = new Page(1, 20); // page 1, 20 items per page

echo $page->getNumber(); // 1
echo $page->getLimit();  // 20

// Create default page (page 1, 10 items)
$page = Page::startDefault();

// Change page number
$page2 = $page->startFrom(2);

// Change limit
$page = $page->limitTo(50);
```

**Methods:**
- `getNumber()` - Get page number
- `getLimit()` - Get items per page
- `startFrom(int $number)` - Create new page with different number
- `limitTo(int $limit)` - Create new page with different limit
- `startDefault()` - Create page 1 with 10 items

### Filter

Represents a search filter.

```php
use Dranzd\Common\Domain\ValueObject\Search\Filter;

$filter = new Filter('status', 'active');

echo $filter->getField();    // "status"
echo $filter->getValue();    // "active"
echo $filter->getOperator(); // "=" (default)

// With custom operator
$filter = new Filter('age', 18, '>');
```

### Sorter

Represents sorting criteria.

```php
use Dranzd\Common\Domain\ValueObject\Search\Sorter;

$sorter = new Sorter('created_at', 'desc');

echo $sorter->getField();     // "created_at"
echo $sorter->getDirection(); // "desc"
```

### Query

Represents a complete search query with filters, sorting, and pagination.

```php
use Dranzd\Common\Domain\ValueObject\Search\Query;
use Dranzd\Common\Domain\ValueObject\Search\Filter;
use Dranzd\Common\Domain\ValueObject\Search\Sorter;
use Dranzd\Common\Domain\ValueObject\Search\Page;

// Create query
$query = Query::create(
    [new Filter('status', 'active')],
    [new Sorter('created_at', 'desc')],
    1,  // page number
    20  // items per page
);

// Or build incrementally
$query = Query::startDefault()
    ->filterBy([new Filter('status', 'active')])
    ->sortBy([new Sorter('created_at', 'desc')]);

// Get components
$filters = $query->getFilters();
$sorters = $query->getSorters();
$page = $query->getPage();
```

### Result

Represents search results.

```php
use Dranzd\Common\Domain\ValueObject\Search\Result;

$result = new Result($items, 100); // items array, total count

echo $result->getTotal(); // 100
$items = $result->getItems();
```

### Criteria

Represents search criteria (combines filters and sorters).

```php
use Dranzd\Common\Domain\ValueObject\Search\Criteria;

$criteria = new Criteria(
    [new Filter('status', 'active')],
    [new Sorter('created_at', 'desc')]
);

$filters = $criteria->getFilters();
$sorters = $criteria->getSorters();
```

---

## Traits

### CompareSameValueTrait

Provides `sameValueAs()` implementation by comparing native values.

```php
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;

class MyValueObject implements ValueObject
{
    use CompareSameValueTrait;
    
    // Automatically implements sameValueAs() by comparing toNative() results
}
```

### ToStringUsingObjectHashTrait

Provides `__toString()` implementation using object hash.

```php
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;

class MyValueObject implements ValueObject
{
    use ToStringUsingObjectHashTrait;
    
    // Automatically implements __toString() using spl_object_hash()
}
```

---

## Interfaces

### ValueObject

Base interface all value objects must implement.

```php
interface ValueObject
{
    public function sameValueAs(ValueObject $object): bool;
    public function __toString(): string;
}
```

### ScalarValueObjectTranslatorCapable

For value objects that can be converted to/from scalar values.

```php
interface ScalarValueObjectTranslatorCapable
{
    public static function fromNative();
    public function toNative();
}
```

### ArrayValueObjectTranslatorCapable

For value objects that can be converted to/from arrays.

```php
interface ArrayValueObjectTranslatorCapable
{
    public static function fromArray(array $array);
    public function toArray(): array;
}
```

---

## See Also

- [Creating Custom Value Objects](creating-value-objects.md)
- [Value Object Examples](../examples/custom-value-object-example.md)
