# 1002 - Advanced Value Object Tests

**Status:** 🔵 Planned  
**Priority:** High  
**Series:** 1000 - Core Infrastructure & Testing  
**Dependencies:** 1000 (Test Infrastructure), 1001 (Core Value Object Tests)

---

## Overview

Implement comprehensive unit tests for advanced value objects including Location (Address), File, Collection, and Search components. These are more complex value objects with richer behavior and validation rules.

## Business Context

Advanced value objects provide specialized functionality:
- **Location/Address**: Geographic and postal address handling
- **File**: File metadata and validation
- **Collection**: Type-safe collections of value objects
- **Search Components**: Query, filter, pagination, and result handling

These components require thorough testing to ensure they handle complex scenarios correctly.

## Objectives

- Achieve 80%+ test coverage for advanced value objects
- Test complex validation rules and business logic
- Verify collection operations and type safety
- Test search component interactions
- Document testing patterns for complex value objects
- Ensure reliability of advanced features

---

## Tasks

### 1. Location Tests

#### Address Value Object
- [ ] Test `fromNative()` with complete address
- [ ] Test `toNative()` returns address array
- [ ] Test `sameValueAs()` equality
- [ ] Test address components (street, city, state, zip, country)
- [ ] Test optional fields (apartment, building)
- [ ] Test various address formats
- [ ] Test international addresses
- [ ] Test address validation rules
- [ ] Test incomplete addresses
- [ ] Test invalid addresses
- [ ] Test immutability
- [ ] Test string representation

### 2. File Tests

#### File Value Object
- [ ] Test `fromNative()` with file data
- [ ] Test `toNative()` returns file array
- [ ] Test `sameValueAs()` equality
- [ ] Test file name validation
- [ ] Test file path handling
- [ ] Test file extension extraction
- [ ] Test MIME type validation
- [ ] Test file size handling
- [ ] Test various file types
- [ ] Test special characters in filenames
- [ ] Test path traversal prevention
- [ ] Test invalid file data
- [ ] Test immutability

### 3. Collection Tests

#### Collection Value Object
- [ ] Test `fromNative()` with array of items
- [ ] Test `toNative()` returns array
- [ ] Test `sameValueAs()` equality
- [ ] Test `count()` returns correct count
- [ ] Test `isEmpty()` for empty collections
- [ ] Test `contains()` for item existence
- [ ] Test `add()` adds items (if mutable)
- [ ] Test `remove()` removes items (if mutable)
- [ ] Test iteration over collection
- [ ] Test array access operations
- [ ] Test type safety (if typed collection)
- [ ] Test empty collection
- [ ] Test large collections
- [ ] Test immutability
- [ ] Test collection operations (map, filter, reduce if available)

### 4. Search Component Tests

#### Criteria Value Object
- [ ] Test `fromNative()` with criteria data
- [ ] Test `toNative()` returns criteria array
- [ ] Test `sameValueAs()` equality
- [ ] Test criteria building
- [ ] Test multiple criteria
- [ ] Test criteria combination (AND/OR)
- [ ] Test empty criteria
- [ ] Test invalid criteria
- [ ] Test immutability

#### Filter Value Object
- [ ] Test `fromNative()` with filter data
- [ ] Test `toNative()` returns filter array
- [ ] Test `sameValueAs()` equality
- [ ] Test filter field and value
- [ ] Test filter operators (equals, contains, greater than, etc.)
- [ ] Test multiple filters
- [ ] Test filter validation
- [ ] Test invalid filters
- [ ] Test immutability

#### Page Value Object
- [ ] Test `fromNative()` with page data
- [ ] Test `toNative()` returns page array
- [ ] Test `sameValueAs()` equality
- [ ] Test page number and size
- [ ] Test offset calculation
- [ ] Test first/last page detection
- [ ] Test invalid page numbers (negative, zero)
- [ ] Test invalid page sizes
- [ ] Test large page numbers
- [ ] Test immutability

#### Query Value Object
- [ ] Test `fromNative()` with query data
- [ ] Test `toNative()` returns query array
- [ ] Test `sameValueAs()` equality
- [ ] Test query string
- [ ] Test query fields
- [ ] Test query operators
- [ ] Test empty query
- [ ] Test complex queries
- [ ] Test query validation
- [ ] Test immutability

#### Result Value Object
- [ ] Test `fromNative()` with result data
- [ ] Test `toNative()` returns result array
- [ ] Test `sameValueAs()` equality
- [ ] Test result items
- [ ] Test total count
- [ ] Test pagination info
- [ ] Test empty results
- [ ] Test large result sets
- [ ] Test result metadata
- [ ] Test immutability

#### Sorter Value Object
- [ ] Test `fromNative()` with sorter data
- [ ] Test `toNative()` returns sorter array
- [ ] Test `sameValueAs()` equality
- [ ] Test sort field and direction
- [ ] Test multiple sort fields
- [ ] Test ascending/descending
- [ ] Test invalid sort fields
- [ ] Test invalid sort directions
- [ ] Test immutability

### 5. Integration Tests

- [ ] Test search components working together
  - [ ] Query + Filter + Page + Sorter
  - [ ] Building complete search request
  - [ ] Processing search results
- [ ] Test collection of value objects
  - [ ] Collection of Money objects
  - [ ] Collection of Uuid objects
  - [ ] Collection of custom value objects
- [ ] Test complex address scenarios
  - [ ] Address normalization
  - [ ] Address comparison
  - [ ] International address handling

### 6. Edge Cases and Error Handling

- [ ] Test null/empty inputs for all value objects
- [ ] Test boundary values
- [ ] Test special characters and encoding
- [ ] Test very large data sets
- [ ] Test concurrent access (if applicable)
- [ ] Test memory efficiency
- [ ] Test exception messages are clear and helpful

### 7. Documentation

- [ ] Add PHPDoc to all test methods
- [ ] Document complex test scenarios
- [ ] Create testing guide for advanced value objects
- [ ] Add examples of integration tests
- [ ] Document performance considerations

---

## Acceptance Criteria

- [ ] All advanced value objects have comprehensive unit tests
- [ ] Test coverage ≥80% for all advanced value objects
- [ ] All public methods are tested
- [ ] Complex scenarios and edge cases are tested
- [ ] Integration between components is tested
- [ ] Error conditions and validation are tested
- [ ] All tests pass with PHPUnit 11
- [ ] Test code follows PSR-12 standards
- [ ] Tests are well-documented
- [ ] Performance tests exist for collections and search

---

## Example Tests

### Address Test Example

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Location;

use Dranzd\Common\Domain\ValueObject\Location\Address;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

final class AddressTest extends ValueObjectTestCase
{
    public function testFromNativeWithCompleteAddress(): void
    {
        $addressData = [
            'street' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701',
            'country' => 'USA',
        ];
        
        $address = Address::fromNative($addressData);
        
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($addressData, $address->toNative());
    }
    
    public function testSameValueAs(): void
    {
        $address1 = Address::fromNative([
            'street' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701',
            'country' => 'USA',
        ]);
        
        $address2 = Address::fromNative([
            'street' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701',
            'country' => 'USA',
        ]);
        
        $address3 = Address::fromNative([
            'street' => '456 Oak Ave',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62702',
            'country' => 'USA',
        ]);
        
        $this->assertTrue($address1->sameValueAs($address2));
        $this->assertFalse($address1->sameValueAs($address3));
    }
    
    /**
     * @dataProvider internationalAddressProvider
     */
    public function testInternationalAddresses(array $addressData): void
    {
        $address = Address::fromNative($addressData);
        
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($addressData['country'], $address->toNative()['country']);
    }
    
    public static function internationalAddressProvider(): array
    {
        return [
            'UK' => [[
                'street' => '10 Downing Street',
                'city' => 'London',
                'zip' => 'SW1A 2AA',
                'country' => 'UK',
            ]],
            'Japan' => [[
                'street' => '1-1 Chiyoda',
                'city' => 'Tokyo',
                'zip' => '100-0001',
                'country' => 'Japan',
            ]],
            'Germany' => [[
                'street' => 'Unter den Linden 77',
                'city' => 'Berlin',
                'zip' => '10117',
                'country' => 'Germany',
            ]],
        ];
    }
}
```

### Collection Test Example

```php
<?php

namespace Dranzd\Common\Tests\Unit\ValueObject\Collection;

use Dranzd\Common\Domain\ValueObject\Collection\Collection;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

final class CollectionTest extends ValueObjectTestCase
{
    public function testFromNativeWithArray(): void
    {
        $items = ['item1', 'item2', 'item3'];
        $collection = Collection::fromNative($items);
        
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($items, $collection->toNative());
        $this->assertCount(3, $collection);
    }
    
    public function testIsEmpty(): void
    {
        $emptyCollection = Collection::fromNative([]);
        $nonEmptyCollection = Collection::fromNative(['item']);
        
        $this->assertTrue($emptyCollection->isEmpty());
        $this->assertFalse($nonEmptyCollection->isEmpty());
    }
    
    public function testIteration(): void
    {
        $items = ['a', 'b', 'c'];
        $collection = Collection::fromNative($items);
        
        $result = [];
        foreach ($collection as $item) {
            $result[] = $item;
        }
        
        $this->assertEquals($items, $result);
    }
    
    public function testCollectionOfValueObjects(): void
    {
        $literal1 = Literal::fromNative('test1');
        $literal2 = Literal::fromNative('test2');
        
        $collection = Collection::fromNative([$literal1, $literal2]);
        
        $this->assertCount(2, $collection);
    }
}
```

### Search Integration Test Example

```php
<?php

namespace Dranzd\Common\Tests\Integration\ValueObject\Search;

use Dranzd\Common\Domain\ValueObject\Search\Query;
use Dranzd\Common\Domain\ValueObject\Search\Filter;
use Dranzd\Common\Domain\ValueObject\Search\Page;
use Dranzd\Common\Domain\ValueObject\Search\Sorter;
use Dranzd\Common\Tests\Unit\ValueObject\ValueObjectTestCase;

final class SearchIntegrationTest extends ValueObjectTestCase
{
    public function testCompleteSearchRequest(): void
    {
        // Build search query
        $query = Query::fromNative(['term' => 'test search']);
        
        // Add filters
        $filter = Filter::fromNative([
            'field' => 'status',
            'operator' => 'equals',
            'value' => 'active',
        ]);
        
        // Set pagination
        $page = Page::fromNative([
            'number' => 1,
            'size' => 20,
        ]);
        
        // Set sorting
        $sorter = Sorter::fromNative([
            'field' => 'created_at',
            'direction' => 'desc',
        ]);
        
        // Verify all components work together
        $this->assertInstanceOf(Query::class, $query);
        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertInstanceOf(Page::class, $page);
        $this->assertInstanceOf(Sorter::class, $sorter);
        
        // Verify serialization
        $searchRequest = [
            'query' => $query->toNative(),
            'filter' => $filter->toNative(),
            'page' => $page->toNative(),
            'sorter' => $sorter->toNative(),
        ];
        
        $this->assertIsArray($searchRequest);
        $this->assertArrayHasKey('query', $searchRequest);
        $this->assertArrayHasKey('filter', $searchRequest);
        $this->assertArrayHasKey('page', $searchRequest);
        $this->assertArrayHasKey('sorter', $searchRequest);
    }
}
```

---

## Test Coverage Goals

| Component | Target Coverage | Priority |
|-----------|----------------|----------|
| Location/Address | 85%+ | High |
| File/File | 85%+ | High |
| Collection/Collection | 90%+ | High |
| Search/Criteria | 80%+ | Medium |
| Search/Filter | 85%+ | High |
| Search/Page | 90%+ | High |
| Search/Query | 85%+ | High |
| Search/Result | 85%+ | High |
| Search/Sorter | 85%+ | High |

---

## Performance Considerations

- Test collection performance with large datasets (1000+ items)
- Test search component memory usage
- Test address validation performance
- Benchmark file metadata operations

---

## Related Features

- **1000 - Test Infrastructure**: Provides base classes and utilities
- **1001 - Core Value Object Tests**: Tests fundamental value objects
- **1003 - PHPStan Cleanup**: Static analysis improvements
- **1004 - CI/CD Pipeline**: Automated testing

---

**Created:** November 9, 2025  
**Target Completion:** November 25, 2025  
**Estimated Effort:** 3-4 days
