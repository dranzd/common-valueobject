# Custom Value Object Examples

Complete examples of custom value objects for common use cases.

## Example 1: Email Address

A simple string-based value object with validation.

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
        $this->value = strtolower($value);
    }
    
    public static function fromNative($value): self
    {
        return new self($value);
    }
    
    public function toNative(): string
    {
        return $this->value;
    }
    
    public function getDomain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }
    
    public function getLocalPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
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

**Usage:**

```php
$email = EmailAddress::fromNative('John.Doe@Example.com');

echo $email->toNative();     // "john.doe@example.com" (normalized)
echo $email->getDomain();    // "example.com"
echo $email->getLocalPart(); // "john.doe"
echo $email;                 // "john.doe@example.com"
```

---

## Example 2: Phone Number

International phone number with country code.

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;

final class PhoneNumber implements ValueObject
{
    use CompareSameValueTrait;
    
    private string $countryCode;
    private string $number;
    
    private function __construct(string $countryCode, string $number)
    {
        $this->validate($countryCode, $number);
        $this->countryCode = $countryCode;
        $this->number = $this->normalize($number);
    }
    
    public static function fromNative($value): self
    {
        if (is_array($value)) {
            return new self($value['country_code'], $value['number']);
        }
        
        // Parse from string like "+1-555-123-4567"
        if (preg_match('/^\+(\d{1,3})-(.+)$/', $value, $matches)) {
            return new self($matches[1], $matches[2]);
        }
        
        throw new \InvalidArgumentException('Invalid phone number format');
    }
    
    public function toNative(): array
    {
        return [
            'country_code' => $this->countryCode,
            'number' => $this->number,
        ];
    }
    
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
    
    public function getNumber(): string
    {
        return $this->number;
    }
    
    public function toInternationalFormat(): string
    {
        return sprintf('+%s-%s', $this->countryCode, $this->number);
    }
    
    private function normalize(string $number): string
    {
        // Remove all non-numeric characters except hyphens
        return preg_replace('/[^\d-]/', '', $number);
    }
    
    private function validate(string $countryCode, string $number): void
    {
        if (!preg_match('/^\d{1,3}$/', $countryCode)) {
            throw new \InvalidArgumentException('Invalid country code');
        }
        
        if (empty($number)) {
            throw new \InvalidArgumentException('Phone number cannot be empty');
        }
    }
    
    public function __toString(): string
    {
        return $this->toInternationalFormat();
    }
}
```

**Usage:**

```php
// From array
$phone = PhoneNumber::fromNative([
    'country_code' => '1',
    'number' => '555-123-4567'
]);

// From string
$phone = PhoneNumber::fromNative('+1-555-123-4567');

echo $phone->getCountryCode();        // "1"
echo $phone->getNumber();             // "555-123-4567"
echo $phone->toInternationalFormat(); // "+1-555-123-4567"
echo $phone;                          // "+1-555-123-4567"
```

---

## Example 3: Price with Tax

Money value object with tax calculation.

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Money\Basic as Money;

final class Price implements ValueObject
{
    use CompareSameValueTrait;
    
    private Money $amount;
    private float $taxRate;
    
    private function __construct(Money $amount, float $taxRate)
    {
        $this->validateTaxRate($taxRate);
        $this->amount = $amount;
        $this->taxRate = $taxRate;
    }
    
    public static function fromNative($value): self
    {
        if (is_array($value)) {
            $amount = Money::fromNative([
                'amount' => $value['amount'],
                'currency' => $value['currency']
            ]);
            return new self($amount, $value['tax_rate'] ?? 0.0);
        }
        
        throw new \InvalidArgumentException('Price must be created from array');
    }
    
    public function toNative(): array
    {
        return [
            'amount' => $this->amount->getAmount(),
            'currency' => $this->amount->getCurrency(),
            'tax_rate' => $this->taxRate,
        ];
    }
    
    public function getAmount(): Money
    {
        return $this->amount;
    }
    
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }
    
    public function getTaxAmount(): int
    {
        return (int) round($this->amount->getAmount() * $this->taxRate);
    }
    
    public function getTotalWithTax(): int
    {
        return $this->amount->getAmount() + $this->getTaxAmount();
    }
    
    public function withTaxRate(float $taxRate): self
    {
        return new self($this->amount, $taxRate);
    }
    
    private function validateTaxRate(float $taxRate): void
    {
        if ($taxRate < 0 || $taxRate > 1) {
            throw new \InvalidArgumentException('Tax rate must be between 0 and 1');
        }
    }
    
    public function __toString(): string
    {
        return sprintf(
            '%d %s (tax: %.1f%%)',
            $this->amount->getAmount(),
            $this->amount->getCurrency(),
            $this->taxRate * 100
        );
    }
}
```

**Usage:**

```php
$price = Price::fromNative([
    'amount' => 10000,      // $100.00
    'currency' => 'USD',
    'tax_rate' => 0.08      // 8%
]);

echo $price->getAmount()->getAmount(); // 10000
echo $price->getTaxRate();             // 0.08
echo $price->getTaxAmount();           // 800 ($8.00)
echo $price->getTotalWithTax();        // 10800 ($108.00)

// Change tax rate
$newPrice = $price->withTaxRate(0.10); // 10%
echo $newPrice->getTotalWithTax();     // 11000 ($110.00)
```

---

## Example 4: Date Range

Value object representing a period between two dates.

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\DateTime\Date;

final class DateRange implements ValueObject
{
    use CompareSameValueTrait;
    
    private Date $startDate;
    private Date $endDate;
    
    private function __construct(Date $startDate, Date $endDate)
    {
        $this->validate($startDate, $endDate);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    public static function fromNative($value): self
    {
        if (is_array($value)) {
            return new self(
                Date::fromNative($value['start_date']),
                Date::fromNative($value['end_date'])
            );
        }
        
        throw new \InvalidArgumentException('DateRange must be created from array');
    }
    
    public static function fromDates(Date $startDate, Date $endDate): self
    {
        return new self($startDate, $endDate);
    }
    
    public function toNative(): array
    {
        return [
            'start_date' => $this->startDate->toNative(),
            'end_date' => $this->endDate->toNative(),
        ];
    }
    
    public function getStartDate(): Date
    {
        return $this->startDate;
    }
    
    public function getEndDate(): Date
    {
        return $this->endDate;
    }
    
    public function getDurationInDays(): int
    {
        $start = new \DateTime($this->startDate->toNative());
        $end = new \DateTime($this->endDate->toNative());
        return $start->diff($end)->days;
    }
    
    public function contains(Date $date): bool
    {
        $dateStr = $date->toNative();
        return $dateStr >= $this->startDate->toNative() 
            && $dateStr <= $this->endDate->toNative();
    }
    
    public function overlaps(DateRange $other): bool
    {
        return $this->startDate->toNative() <= $other->endDate->toNative()
            && $this->endDate->toNative() >= $other->startDate->toNative();
    }
    
    private function validate(Date $startDate, Date $endDate): void
    {
        if ($startDate->toNative() > $endDate->toNative()) {
            throw new \InvalidArgumentException('Start date must be before or equal to end date');
        }
    }
    
    public function __toString(): string
    {
        return sprintf(
            '%s to %s',
            $this->startDate->toNative(),
            $this->endDate->toNative()
        );
    }
}
```

**Usage:**

```php
$range = DateRange::fromNative([
    'start_date' => '2025-01-01',
    'end_date' => '2025-12-31'
]);

// Or from Date objects
$start = Date::fromNative('2025-01-01');
$end = Date::fromNative('2025-12-31');
$range = DateRange::fromDates($start, $end);

echo $range->getDurationInDays(); // 364

$someDate = Date::fromNative('2025-06-15');
$range->contains($someDate); // true

$otherRange = DateRange::fromNative([
    'start_date' => '2025-11-01',
    'end_date' => '2026-01-31'
]);
$range->overlaps($otherRange); // true
```

---

## Example 5: Percentage

Value object for percentage values with validation.

```php
<?php

namespace App\Domain\ValueObject;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;

final class Percentage implements ValueObject
{
    use CompareSameValueTrait;
    
    private float $value;
    
    private function __construct(float $value)
    {
        $this->validate($value);
        $this->value = $value;
    }
    
    public static function fromNative($value): self
    {
        return new self((float) $value);
    }
    
    public static function fromDecimal(float $decimal): self
    {
        return new self($decimal * 100);
    }
    
    public function toNative(): float
    {
        return $this->value;
    }
    
    public function toDecimal(): float
    {
        return $this->value / 100;
    }
    
    public function applyTo(int $amount): int
    {
        return (int) round($amount * $this->toDecimal());
    }
    
    public function increase(float $percentage): self
    {
        return new self($this->value + $percentage);
    }
    
    public function decrease(float $percentage): self
    {
        return new self($this->value - $percentage);
    }
    
    private function validate(float $value): void
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('Percentage must be between 0 and 100');
        }
    }
    
    public function __toString(): string
    {
        return sprintf('%.2f%%', $this->value);
    }
}
```

**Usage:**

```php
$discount = Percentage::fromNative(15); // 15%
echo $discount->toNative();    // 15.0
echo $discount->toDecimal();   // 0.15
echo $discount;                // "15.00%"

// Apply to amount
$price = 10000; // $100.00
$discountAmount = $discount->applyTo($price); // 1500 ($15.00)

// Create from decimal
$tax = Percentage::fromDecimal(0.08); // 8%
echo $tax; // "8.00%"

// Modify
$newDiscount = $discount->increase(5); // 20%
```

---

## See Also

- [Creating Custom Value Objects](../guides/creating-value-objects.md)
- [Built-in Value Objects](../guides/built-in-value-objects.md)
