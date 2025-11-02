# Creating Custom Value Objects

This guide explains how to create custom value objects using the `dranzd/common-valueobject` library.

## What is a Value Object?

A Value Object is an immutable object that represents a descriptive aspect of the domain with no conceptual identity. Two value objects are equal if all their attributes are equal.

### Key Characteristics:
1. **Immutable** - Once created, cannot be changed
2. **Self-Validating** - Invalid states are impossible
3. **Equality by Value** - Compared by their attributes, not identity
4. **Side-Effect Free** - Operations return new instances

---

## Basic Value Object

### Step 1: Implement the ValueObject Interface

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;

final class EmailAddress implements ValueObject
{
    use CompareSameValueTrait;
    
    private string $value;
    
    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }
    
    public static function fromNative($value): self
    {
        return new self($value);
    }
    
    public function toNative(): string
    {
        return $this->value;
    }
    
    private function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid email address', $value)
            );
        }
    }
    
    public function __toString(): string
    {
        return $this->value;
    }
}
```

### Step 2: Usage

```php
// Create value object
$email = EmailAddress::fromNative('user@example.com');

// Get native value
echo $email->toNative(); // "user@example.com"

// String conversion
echo $email; // "user@example.com"

// Comparison
$email1 = EmailAddress::fromNative('user@example.com');
$email2 = EmailAddress::fromNative('user@example.com');
$email3 = EmailAddress::fromNative('other@example.com');

$email1->sameValueAs($email2); // true
$email1->sameValueAs($email3); // false

// Validation happens automatically
try {
    $invalid = EmailAddress::fromNative('not-an-email');
} catch (\InvalidArgumentException $e) {
    // Exception thrown
}
```

---

## Extending Built-in Value Objects

### Extending String Literal

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\DomainAssert\Assertion;

final class Username extends Literal
{
    public function __construct($value = null)
    {
        Assertion::string($value);
        Assertion::minLength($value, 3, 'Username must be at least 3 characters');
        Assertion::maxLength($value, 20, 'Username must be at most 20 characters');
        Assertion::regex($value, '/^[a-zA-Z0-9_]+$/', 'Username can only contain letters, numbers, and underscores');
        
        $this->value = $value;
    }
}
```

### Usage

```php
$username = Username::fromNative('john_doe');
echo $username->toNative(); // "john_doe"

// Validation
Username::fromNative('ab'); // Exception: too short
Username::fromNative('user@name'); // Exception: invalid characters
```

---

## Complex Value Objects

### Value Object with Multiple Properties

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;

final class Coordinates implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;
    
    private float $latitude;
    private float $longitude;
    
    private function __construct(float $latitude, float $longitude)
    {
        $this->validate($latitude, $longitude);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
    
    public static function fromNative(): self
    {
        $args = func_get_args();
        
        if (count($args) === 1 && is_array($args[0])) {
            return self::fromArray($args[0]);
        }
        
        return new self($args[0], $args[1]);
    }
    
    public static function fromArray(array $data): self
    {
        return new self(
            $data['latitude'] ?? $data['lat'],
            $data['longitude'] ?? $data['lng']
        );
    }
    
    public function toNative(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
    
    public function toArray(): array
    {
        return $this->toNative();
    }
    
    public function getLatitude(): float
    {
        return $this->latitude;
    }
    
    public function getLongitude(): float
    {
        return $this->longitude;
    }
    
    private function validate(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90');
        }
        
        if ($longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180');
        }
    }
    
    public function __toString(): string
    {
        return sprintf('%.6f, %.6f', $this->latitude, $this->longitude);
    }
}
```

### Usage

```php
// Create from separate values
$coords = Coordinates::fromNative(40.7128, -74.0060);

// Create from array
$coords = Coordinates::fromArray([
    'latitude' => 40.7128,
    'longitude' => -74.0060
]);

// Get values
echo $coords->getLatitude();  // 40.7128
echo $coords->getLongitude(); // -74.0060

// Convert to array
$array = $coords->toArray();
// ['latitude' => 40.7128, 'longitude' => -74.0060]

// String representation
echo $coords; // "40.712800, -74.006000"
```

---

## Best Practices

### 1. Make Constructors Private

Always use static factory methods (`fromNative()`) instead of public constructors:

```php
// ✅ Good
private function __construct(string $value) { ... }
public static function fromNative($value): self { ... }

// ❌ Bad
public function __construct(string $value) { ... }
```

### 2. Validate in Constructor

Perform all validation in the constructor to ensure invalid states are impossible:

```php
private function __construct(string $value)
{
    $this->validate($value); // Validate first
    $this->value = $value;   // Then assign
}
```

### 3. Use Assertions

Use the `Dranzd\Common\DomainAssert\Assertion` class for validation:

```php
use Dranzd\Common\DomainAssert\Assertion;

Assertion::string($value);
Assertion::notEmpty($value);
Assertion::email($value);
Assertion::uuid($value);
```

### 4. Provide Named Constructors

For complex value objects, provide expressive named constructors:

```php
public static function fromString(string $value): self { ... }
public static function fromArray(array $data): self { ... }
public static function fromCoordinates(float $lat, float $lng): self { ... }
```

### 5. Make Value Objects Final

Prevent inheritance to maintain immutability guarantees:

```php
final class EmailAddress implements ValueObject { ... }
```

### 6. Use Type Hints

Always use strict type hints for better type safety:

```php
public function __construct(string $value) { ... }
public function toNative(): string { ... }
```

---

## Testing Value Objects

```php
<?php

namespace Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\EmailAddress;

class EmailAddressTest extends TestCase
{
    public function testCanCreateFromValidEmail(): void
    {
        $email = EmailAddress::fromNative('user@example.com');
        
        $this->assertSame('user@example.com', $email->toNative());
    }
    
    public function testThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        EmailAddress::fromNative('not-an-email');
    }
    
    public function testValueObjectsWithSameValueAreEqual(): void
    {
        $email1 = EmailAddress::fromNative('user@example.com');
        $email2 = EmailAddress::fromNative('user@example.com');
        
        $this->assertTrue($email1->sameValueAs($email2));
    }
    
    public function testValueObjectsWithDifferentValueAreNotEqual(): void
    {
        $email1 = EmailAddress::fromNative('user1@example.com');
        $email2 = EmailAddress::fromNative('user2@example.com');
        
        $this->assertFalse($email1->sameValueAs($email2));
    }
    
    public function testCanConvertToString(): void
    {
        $email = EmailAddress::fromNative('user@example.com');
        
        $this->assertSame('user@example.com', (string) $email);
    }
}
```

---

## Common Patterns

### Enum-like Value Objects

```php
final class OrderStatus extends Literal
{
    private const PENDING = 'pending';
    private const CONFIRMED = 'confirmed';
    private const SHIPPED = 'shipped';
    private const DELIVERED = 'delivered';
    
    private const VALID_STATUSES = [
        self::PENDING,
        self::CONFIRMED,
        self::SHIPPED,
        self::DELIVERED,
    ];
    
    public function __construct($value = null)
    {
        Assertion::inArray($value, self::VALID_STATUSES, 'Invalid order status');
        $this->value = $value;
    }
    
    public static function pending(): self
    {
        return new self(self::PENDING);
    }
    
    public static function confirmed(): self
    {
        return new self(self::CONFIRMED);
    }
    
    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }
}
```

### Collection Value Objects

```php
final class EmailList implements ValueObject
{
    use CompareSameValueTrait;
    
    private array $emails;
    
    private function __construct(array $emails)
    {
        $this->emails = array_map(
            fn($email) => $email instanceof EmailAddress 
                ? $email 
                : EmailAddress::fromNative($email),
            $emails
        );
    }
    
    public static function fromNative($value): self
    {
        return new self(is_array($value) ? $value : [$value]);
    }
    
    public function toNative(): array
    {
        return array_map(
            fn(EmailAddress $email) => $email->toNative(),
            $this->emails
        );
    }
    
    public function count(): int
    {
        return count($this->emails);
    }
    
    public function __toString(): string
    {
        return implode(', ', $this->toNative());
    }
}
```

---

## See Also

- [Built-in Value Objects](built-in-value-objects.md)
- [Value Object Examples](../examples/custom-value-object-example.md)
