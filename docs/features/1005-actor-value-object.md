# 1005 - Actor Value Object

**Status:** 🔵 Planned  
**Priority:** High  
**Series:** 2000 - Additional Value Object Types  
**Dependencies:** None

---

## Overview

The **Actor Value Object** represents *who or what initiated a domain action* within domain-driven applications. It provides consistent identity information across domains for actions such as stock consumption, adjustments, approvals, or automated system processes.

It replaces simple string references like `consumedBy` or `createdBy` with a structured, strongly-typed value object that captures identity and source context.

## Business Context

In event-sourced and domain-driven systems, tracking the initiator of actions is critical for:
- **Auditability**: Compliance and accountability requirements
- **Multi-tenancy**: Tracking actions across different tenants and branches
- **Automation**: Distinguishing between user and system-initiated actions
- **Integration**: Identifying external system triggers

## Objectives

- Provide a reusable, immutable value object for actor identity
- Support multiple actor types (User, System, Integration, Unknown)
- Enable consistent event sourcing and audit trails
- Maintain tenant and branch context for multi-tenant systems

---

## Tasks

### 1. Core Implementation
- [ ] Create `ActorType` enum with values: User, System, Integration, Unknown
- [ ] Create `Actor` value object class in `src/Domain/ValueObject/Identity/`
- [ ] Implement immutable properties: id, type, name, tenantId, branchId, sourceSystem
- [ ] Implement `ValueObject` interface with `fromNative()` and `toNative()` methods
- [ ] Add `CompareSameValueTrait` for equality comparison
- [ ] Implement `__toString()` method

### 2. Factory Methods
- [ ] Create `Actor::user()` static factory method
- [ ] Create `Actor::system()` static factory method
- [ ] Create `Actor::integration()` static factory method
- [ ] Create `Actor::unknown()` static factory method

### 3. Validation & Rules
- [ ] Validate required fields based on actor type
- [ ] Ensure immutability (no setters)
- [ ] Add appropriate type hints and return types

### 4. Testing
- [ ] Create unit tests for Actor value object
- [ ] Test all factory methods
- [ ] Test equality comparison
- [ ] Test serialization/deserialization
- [ ] Test validation rules
- [ ] Achieve 80%+ code coverage

### 5. Documentation
- [ ] Add PHPDoc comments to all public methods
- [ ] Create usage examples in documentation
- [ ] Update README with Actor example
- [ ] Add to value object catalog

---

## Acceptance Criteria

- ✅ Actor value object is immutable
- ✅ All four actor types are supported (User, System, Integration, Unknown)
- ✅ Factory methods create properly configured actors
- ✅ Implements ValueObject interface correctly
- ✅ Equality comparison works correctly
- ✅ Serializes to/from native arrays for event storage
- ✅ All tests pass with 80%+ coverage
- ✅ PHPStan level 8+ passes
- ✅ Code style (PSR-12) passes
- ✅ Documentation is complete with examples

---

## Technical Design

### Value Object Fields

| Field | Type | Required | Description |
|--------|------|-----------|-------------|
| `id` | string / null | Optional | Unique reference (e.g., user ID, system ID, or external reference). |
| `type` | enum(`User`, `System`, `Integration`, `Unknown`) | ✅ | Defines what kind of actor initiated the action. |
| `name` | string / null | Optional | Human-readable label for the actor (e.g., "John Doe", "ReorderJob"). |
| `tenantId` | string / null | Optional | Identifies the tenant (business) under which the actor operates. |
| `branchId` | string / null | Optional | Identifies the branch, warehouse, or retail location of origin. |
| `sourceSystem` | string / null | Optional | The originating system name. |

These explicit fields replace ambiguous "context" fields and provide structured identity information that's predictable, auditable, and machine-readable.

### Behavior and Rules

- The **Actor Value Object is immutable** (no setters after construction)
- It should be treated as a **shared kernel component** — reusable across all bounded contexts
- Each domain event or command that originates from a user/system should include an `Actor`
- When serialized in events, all Actor fields must be included to ensure replay consistency
- For user-triggered actions, both `id` and `name` are expected
- For system/integration-triggered actions, `name` and `sourceSystem` suffice

### Actor Types

An **Actor** can be one of four types:
- **User** – A person performing an action (e.g., store operator, manager)
- **System** – A background process or service (e.g., automated reorder, cron job)
- **Integration** – An external API client or partner system
- **Unknown** – A fallback when the initiator cannot be identified

---

## Example Usage

### PHP Implementation

```php
<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\ValueObject;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;

enum ActorType: string
{
    case User = 'User';
    case System = 'System';
    case Integration = 'Integration';
    case Unknown = 'Unknown';
}

final class Actor implements ValueObject
{
    use CompareSameValueTrait;
    
    private function __construct(
        private readonly ?string $id,
        private readonly ActorType $type,
        private readonly ?string $name,
        private readonly ?string $tenantId = null,
        private readonly ?string $branchId = null,
        private readonly ?string $sourceSystem = null,
    ) {}
    
    public static function user(
        string $id,
        string $name,
        string $tenantId,
        ?string $branchId = null,
        ?string $sourceSystem = 'Application'
    ): self {
        return new self($id, ActorType::User, $name, $tenantId, $branchId, $sourceSystem);
    }
    
    public static function system(string $name, string $sourceSystem): self
    {
        return new self(null, ActorType::System, $name, null, null, $sourceSystem);
    }
    
    public static function integration(string $id, string $name, string $sourceSystem): self
    {
        return new self($id, ActorType::Integration, $name, null, null, $sourceSystem);
    }
    
    public static function unknown(): self
    {
        return new self(null, ActorType::Unknown, null);
    }
    
    public static function fromNative($value): self
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Actor must be created from an array');
        }
        
        return new self(
            $value['id'] ?? null,
            ActorType::from($value['type']),
            $value['name'] ?? null,
            $value['tenantId'] ?? null,
            $value['branchId'] ?? null,
            $value['sourceSystem'] ?? null,
        );
    }
    
    public function toNative(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'name' => $this->name,
            'tenantId' => $this->tenantId,
            'branchId' => $this->branchId,
            'sourceSystem' => $this->sourceSystem,
        ];
    }
    
    public function id(): ?string
    {
        return $this->id;
    }
    
    public function type(): ActorType
    {
        return $this->type;
    }
    
    public function name(): ?string
    {
        return $this->name;
    }
    
    public function tenantId(): ?string
    {
        return $this->tenantId;
    }
    
    public function branchId(): ?string
    {
        return $this->branchId;
    }
    
    public function sourceSystem(): ?string
    {
        return $this->sourceSystem;
    }
    
    public function __toString(): string
    {
        return sprintf(
            '%s: %s (%s)',
            $this->type->value,
            $this->name ?? 'Unknown',
            $this->id ?? 'N/A'
        );
    }
}
```

### Usage Examples

```php
// Create a user actor
$userActor = Actor::user(
    id: 'usr-102',
    name: 'Jane Doe',
    tenantId: 'TENANT-01',
    branchId: 'BRANCH-3'
);

// Create a system actor
$systemActor = Actor::system(
    name: 'AutoReplenishmentJob',
    sourceSystem: 'InventorySystem'
);

// Create an integration actor
$integrationActor = Actor::integration(
    id: 'api-client-123',
    name: 'ExternalAPI',
    sourceSystem: 'PartnerSystem'
);

// Create an unknown actor
$unknownActor = Actor::unknown();

// Serialize for event storage
$eventData = [
    'event' => 'StockConsumed',
    'itemId' => 'PENCIL-HB-001',
    'quantity' => 3,
    'consumedBy' => $userActor->toNative(),
    'timestamp' => '2025-11-07T09:24:00Z',
];

// Deserialize from event
$actor = Actor::fromNative($eventData['consumedBy']);
```

### Domain Event Examples

**Stock Consumed Event:**

```json
{
  "event": "StockConsumed",
  "itemId": "PENCIL-HB-001",
  "quantity": 3,
  "consumedBy": {
    "id": "usr-102",
    "type": "User",
    "name": "Jane Doe",
    "tenantId": "TENANT-01",
    "branchId": "BRANCH-3",
    "sourceSystem": "Application"
  },
  "timestamp": "2025-11-07T09:24:00Z"
}
```

**System-Generated Event:**

```json
{
  "event": "ReorderTriggered",
  "itemId": "PENCIL-HB-001",
  "triggeredBy": {
    "id": null,
    "type": "System",
    "name": "AutoReplenishmentJob",
    "tenantId": "TENANT-01",
    "branchId": null,
    "sourceSystem": "InventorySystem"
  }
}
```

---

## Benefits

- **Auditability** — Every event and command explicitly identifies who or what caused it
- **Cross-domain consistency** — Common identity structure across all bounded contexts
- **Automation support** — Makes system-triggered actions traceable
- **Event replay safety** — Replayed events retain immutable initiator details
- **Tenant/Branch clarity** — Maintains multi-tenant awareness across all actions
- **Type safety** — Enum-based actor types prevent invalid values

---

## Related Features

- **1001** - Core Value Object Tests (for testing Actor)
- **UUID Value Object** - Often used for actor IDs

---

**Created:** 2025-11-07  
**Target Completion:** TBD  
**Estimated Effort:** 2-3 days
