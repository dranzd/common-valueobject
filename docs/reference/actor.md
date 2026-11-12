# Actor

`Dranzd\Common\Domain\ValueObject\Identity\Actor` and `ActorType`. An `Actor` records who or what initiated a domain
action: a user, a system process, an integration, or an unknown initiator. It is a final, immutable
`ArrayValueObjectTranslatorCapable` with private readonly properties.

## `ActorType`

String-backed enum: `User`, `System`, `Integration`, `Unknown` (value equals name, case-sensitive).

## `Actor`

```php
public static function user(
    string $id, string $name, string $tenantId, ?string $branchId = null, ?string $sourceSystem = 'Application'
): self;
public static function system(string $name, string $sourceSystem): self;
public static function integration(string $id, string $name, string $sourceSystem): self;
public static function unknown(): self;
public static function fromArray(array $array): static;
public function toArray(): array;

public function getId(): ?string;
public function getType(): ActorType;
public function getName(): ?string;
public function getTenantId(): ?string;
public function getBranchId(): ?string;
public function getSourceSystem(): ?string;
public function isUser(): bool;
public function isSystem(): bool;
public function isIntegration(): bool;
public function isUnknown(): bool;
public function sameValueAs(ValueObject $object): bool;
public function __toString(): string;   // "User: Jane (usr-1)", "Unknown: Unknown (N/A)"
```

| Factory | id | tenantId | branchId | sourceSystem |
|---|---|---|---|---|
| `user()` | given | given | optional | optional, defaults to `'Application'` |
| `system()` | null | null | null | given |
| `integration()` | given | null | null | given |
| `unknown()` | null | null | null | null |

### Array form

`toArray()` returns the keys `id`, `type` (the enum value), `name`, `tenantId`, `branchId`, `sourceSystem`, in that
order. `fromArray()` reads the same keys:

- `type` is required. Missing or null throws `\InvalidArgumentException('Actor type is required')`.
- A `type` string that is not an `ActorType` value (including a wrong case such as `'user'`) throws
  `\InvalidArgumentException` listing the allowed types, with the `\ValueError` as previous exception.
- The other keys are optional (absent or null gives null); unknown keys are ignored.
- Values are not coerced: a non-string `type`, `id`, `name`, `tenantId`, `branchId` or `sourceSystem` (for example an
  integer) raises a `\TypeError`, not an `\InvalidArgumentException`: `Actor::fromArray(['type' => 5])` is a
  `\TypeError`. These are the only two exception types `fromArray()` throws.

Two actors are `sameValueAs()` when their `toArray()` results are identical.

### Example

```php
use Dranzd\Common\Domain\ValueObject\Identity\Actor;

$actor = Actor::user('usr-1', 'Jane', 'tenant-1');

echo json_encode($actor->toArray());
// {"id":"usr-1","type":"User","name":"Jane","tenantId":"tenant-1","branchId":null,"sourceSystem":"Application"}
echo $actor;                                            // User: Jane (usr-1)
var_dump($actor->sameValueAs(Actor::fromArray($actor->toArray())));   // bool(true)

try {
    Actor::fromArray(['type' => 'Robot']);
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();   // Invalid actor type "Robot"; allowed types: User, System, Integration, Unknown
}
```

`Actor` has no `JsonSerializable`; `json_encode($actor)` yields `{}`. Encode `$actor->toArray()` instead.
