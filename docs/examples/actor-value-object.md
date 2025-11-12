# Actor Value Object - Usage Examples

The **Actor Value Object** represents who or what initiated a domain action. It provides consistent identity information across domains for audit trails, event sourcing, and multi-tenant systems.

## Table of Contents

- [Basic Usage](#basic-usage)
- [Factory Methods](#factory-methods)
- [Domain Events](#domain-events)
- [Serialization](#serialization)
- [Helper Methods](#helper-methods)
- [Best Practices](#best-practices)

## Basic Usage

### Creating Actors

```php
use Dranzd\Common\Domain\ValueObject\Identity\Actor;
use Dranzd\Common\Domain\ValueObject\Identity\ActorType;

// Create a user actor
$userActor = Actor::user(
    id: 'usr-123',
    name: 'John Doe',
    tenantId: 'tenant-1',
    branchId: 'branch-5'
);

// Create a system actor
$systemActor = Actor::system(
    name: 'AutoReplenishmentJob',
    sourceSystem: 'InventorySystem'
);

// Create an integration actor
$integrationActor = Actor::integration(
    id: 'api-client-456',
    name: 'ExternalAPI',
    sourceSystem: 'PartnerSystem'
);

// Create an unknown actor (fallback)
$unknownActor = Actor::unknown();
```

## Factory Methods

### User Actor

For human users performing actions:

```php
$actor = Actor::user(
    id: 'usr-789',           // User identifier
    name: 'Jane Smith',      // Display name
    tenantId: 'tenant-2',    // Tenant/organization
    branchId: 'branch-3',    // Optional: specific branch
    sourceSystem: 'WebApp'   // Optional: defaults to 'Application'
);

echo $actor->id();           // 'usr-789'
echo $actor->name();         // 'Jane Smith'
echo $actor->type()->value;  // 'User'
```

### System Actor

For automated processes:

```php
$actor = Actor::system(
    name: 'NightlyBatchJob',
    sourceSystem: 'CronSystem'
);

echo $actor->name();         // 'NightlyBatchJob'
echo $actor->type()->value;  // 'System'
echo $actor->id();           // null (systems don't have user IDs)
```

### Integration Actor

For external API clients:

```php
$actor = Actor::integration(
    id: 'partner-api-001',
    name: 'PartnerIntegration',
    sourceSystem: 'ExternalPartner'
);

echo $actor->id();           // 'partner-api-001'
echo $actor->type()->value;  // 'Integration'
```

### Unknown Actor

Fallback when initiator cannot be identified:

```php
$actor = Actor::unknown();

echo $actor->type()->value;  // 'Unknown'
echo $actor->id();           // null
echo $actor->name();         // null
```

## Domain Events

### Using Actor in Events

```php
use Dranzd\Common\Domain\ValueObject\Identity\Actor;

class StockConsumedEvent
{
    public function __construct(
        private string $itemId,
        private int $quantity,
        private Actor $consumedBy,
        private \DateTimeImmutable $timestamp
    ) {}

    public function toArray(): array
    {
        return [
            'event' => 'StockConsumed',
            'itemId' => $this->itemId,
            'quantity' => $this->quantity,
            'consumedBy' => $this->consumedBy->toNative(),
            'timestamp' => $this->timestamp->format('c'),
        ];
    }
}

// Create event with user actor
$actor = Actor::user('usr-123', 'John Doe', 'tenant-1', 'branch-5');
$event = new StockConsumedEvent(
    itemId: 'ITEM-001',
    quantity: 5,
    consumedBy: $actor,
    timestamp: new \DateTimeImmutable()
);

// Serialize for storage
$eventData = $event->toArray();
```

### Event Payload Example

```json
{
  "event": "StockConsumed",
  "itemId": "ITEM-001",
  "quantity": 5,
  "consumedBy": {
    "id": "usr-123",
    "type": "User",
    "name": "John Doe",
    "tenantId": "tenant-1",
    "branchId": "branch-5",
    "sourceSystem": "Application"
  },
  "timestamp": "2025-11-13T00:00:00+00:00"
}
```

## Serialization

### To Native Array

```php
$actor = Actor::user('usr-123', 'John Doe', 'tenant-1');

$native = $actor->toNative();
// Returns:
// [
//     'id' => 'usr-123',
//     'type' => 'User',
//     'name' => 'John Doe',
//     'tenantId' => 'tenant-1',
//     'branchId' => null,
//     'sourceSystem' => 'Application'
// ]
```

### From Native Array

```php
$data = [
    'id' => 'usr-456',
    'type' => 'User',
    'name' => 'Jane Doe',
    'tenantId' => 'tenant-2',
    'branchId' => 'branch-3',
    'sourceSystem' => 'Application',
];

$actor = Actor::fromNative($data);

echo $actor->id();    // 'usr-456'
echo $actor->name();  // 'Jane Doe'
```

### JSON Serialization

```php
$actor = Actor::system('BackgroundJob', 'CronSystem');

// Serialize to JSON
$json = json_encode($actor->toNative());

// Deserialize from JSON
$decoded = json_decode($json, true);
$reconstructed = Actor::fromNative($decoded);

// Actors are equal
$actor->sameValueAs($reconstructed); // true
```

## Helper Methods

### Type Checking

```php
$userActor = Actor::user('usr-1', 'Test', 'tenant-1');
$systemActor = Actor::system('Job', 'System');

// Check actor type
$userActor->isUser();         // true
$userActor->isSystem();       // false
$userActor->isIntegration();  // false
$userActor->isUnknown();      // false

$systemActor->isSystem();     // true
$systemActor->isUser();       // false
```

### Accessing Properties

```php
$actor = Actor::user(
    id: 'usr-complete',
    name: 'Complete User',
    tenantId: 'tenant-1',
    branchId: 'branch-1',
    sourceSystem: 'CustomApp'
);

$actor->id();            // 'usr-complete'
$actor->type();          // ActorType::User
$actor->name();          // 'Complete User'
$actor->tenantId();      // 'tenant-1'
$actor->branchId();      // 'branch-1'
$actor->sourceSystem();  // 'CustomApp'
```

### String Representation

```php
$actor = Actor::user('usr-123', 'John Doe', 'tenant-1');

echo $actor;  // "User: John Doe (usr-123)"
echo (string) $actor;  // "User: John Doe (usr-123)"

$unknownActor = Actor::unknown();
echo $unknownActor;  // "Unknown: Unknown (N/A)"
```

## Best Practices

### 1. Use in Domain Events

Always include Actor in domain events for audit trails:

```php
class OrderPlacedEvent
{
    public function __construct(
        private string $orderId,
        private Actor $placedBy,  // ✅ Include actor
        private \DateTimeImmutable $placedAt
    ) {}
}
```

### 2. Multi-Tenant Context

Include tenant and branch information for user actors:

```php
// ✅ Good: Includes tenant and branch context
$actor = Actor::user(
    id: 'usr-123',
    name: 'John Doe',
    tenantId: 'tenant-1',
    branchId: 'branch-5'
);

// ❌ Avoid: Missing context (though still valid)
$actor = Actor::user(
    id: 'usr-123',
    name: 'John Doe',
    tenantId: 'tenant-1'
);
```

### 3. System Actors

Use descriptive names for system actors:

```php
// ✅ Good: Clear, descriptive name
$actor = Actor::system('NightlyInventoryReconciliation', 'InventorySystem');

// ❌ Avoid: Vague name
$actor = Actor::system('Job1', 'System');
```

### 4. Equality Comparison

Use `sameValueAs()` for comparing actors:

```php
$actor1 = Actor::user('usr-1', 'Test', 'tenant-1');
$actor2 = Actor::user('usr-1', 'Test', 'tenant-1');
$actor3 = Actor::user('usr-2', 'Test', 'tenant-1');

$actor1->sameValueAs($actor2);  // true
$actor1->sameValueAs($actor3);  // false
```

### 5. Event Replay

Ensure all actor fields are serialized for event replay:

```php
// ✅ Good: Full serialization
$eventData = [
    'event' => 'ActionPerformed',
    'actor' => $actor->toNative(),  // All fields included
];

// ❌ Avoid: Partial data
$eventData = [
    'event' => 'ActionPerformed',
    'userId' => $actor->id(),  // Missing context
];
```

### 6. Unknown Actors

Use `Actor::unknown()` as a fallback, not as default:

```php
// ✅ Good: Try to identify actor, fallback to unknown
$actor = $this->identifyActor($request) ?? Actor::unknown();

// ❌ Avoid: Always using unknown
$actor = Actor::unknown();  // Loses audit trail
```

## Common Patterns

### Command Handler Pattern

```php
class ConsumeStockCommandHandler
{
    public function handle(ConsumeStockCommand $command): void
    {
        $actor = Actor::user(
            id: $command->userId(),
            name: $command->userName(),
            tenantId: $command->tenantId(),
            branchId: $command->branchId()
        );

        $event = new StockConsumedEvent(
            itemId: $command->itemId(),
            quantity: $command->quantity(),
            consumedBy: $actor,
            occurredAt: new \DateTimeImmutable()
        );

        $this->eventStore->append($event);
    }
}
```

### Audit Log Pattern

```php
class AuditLogger
{
    public function logAction(string $action, Actor $actor, array $details): void
    {
        $logEntry = [
            'action' => $action,
            'actor' => $actor->toNative(),
            'details' => $details,
            'timestamp' => (new \DateTimeImmutable())->format('c'),
        ];

        $this->storage->save($logEntry);
    }
}

// Usage
$actor = Actor::user('usr-123', 'John Doe', 'tenant-1');
$logger->logAction('stock.consumed', $actor, ['itemId' => 'ITEM-001', 'quantity' => 5]);
```

---

**See Also:**
- [Feature 1005 - Actor Value Object](../features/1005-actor-value-object.md)
- [Test Infrastructure](../features/1000-test-infrastructure.md)
- [Value Object Tests](../../tests/Unit/ValueObject/Identity/ActorTest.php)
