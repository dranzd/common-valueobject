<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * Who or what initiated a domain action: a user, a system process, an integration or an unknown initiator.
 *
 * Instances are immutable. Use the named factories, or {@see self::fromArray()} to rebuild one from the output of
 * {@see self::toArray()}.
 *
 * ```php
 * // An order placed by a signed-in user.
 * $actor = Actor::user('usr-123', 'John Doe', 'tenant-1', 'branch-5');
 *
 * // A nightly job acting on its own.
 * $job = Actor::system('NightlyInvoiceRun', 'Billing');
 *
 * // Store it with the event that records the action.
 * $eventData = ['event' => 'OrderPlaced', 'placedBy' => $actor->toArray()];
 * ```
 */
final class Actor implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;

    /**
     * @param string|null $id Unique reference (e.g., user ID, system ID, external reference)
     * @param ActorType $type The type of actor (User, System, Integration, Unknown)
     * @param string|null $name Human-readable label for the actor
     * @param string|null $tenantId Identifies the tenant (business) under which the actor operates
     * @param string|null $branchId Identifies the branch, warehouse, or retail location
     * @param string|null $sourceSystem The originating system name
     */
    private function __construct(
        private readonly ?string $id,
        private readonly ActorType $type,
        private readonly ?string $name,
        private readonly ?string $tenantId = null,
        private readonly ?string $branchId = null,
        private readonly ?string $sourceSystem = null,
    ) {
    }

    /**
     * Check if this actor is equal to another value object.
     *
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    /**
     * Create a user actor.
     *
     * Represents a human user performing an action.
     *
     * @param string $id User identifier
     * @param string $name User's display name
     * @param string $tenantId Tenant identifier
     * @param string|null $branchId Optional branch identifier
     * @param string|null $sourceSystem Optional source system name (defaults to 'Application')
     * @return self
     */
    public static function user(
        string $id,
        string $name,
        string $tenantId,
        ?string $branchId = null,
        ?string $sourceSystem = 'Application'
    ): self {
        return new self($id, ActorType::User, $name, $tenantId, $branchId, $sourceSystem);
    }

    /**
     * Create a system actor.
     *
     * Represents an automated process or background job.
     *
     * @param string $name System process name
     * @param string $sourceSystem Source system identifier
     * @return self
     */
    public static function system(string $name, string $sourceSystem): self
    {
        return new self(null, ActorType::System, $name, null, null, $sourceSystem);
    }

    /**
     * Create an integration actor.
     *
     * Represents an external API client or integration partner.
     *
     * @param string $id Integration client identifier
     * @param string $name Integration name
     * @param string $sourceSystem Source system identifier
     * @return self
     */
    public static function integration(string $id, string $name, string $sourceSystem): self
    {
        return new self($id, ActorType::Integration, $name, null, null, $sourceSystem);
    }

    /**
     * Create an unknown actor.
     *
     * Fallback when the initiator cannot be identified.
     *
     * @return self
     */
    public static function unknown(): self
    {
        return new self(null, ActorType::Unknown, null);
    }

    /**
     * Create an Actor from array representation.
     *
     * Keys: `type` (required, an {@see ActorType} value, case-sensitive), `id`, `name`, `tenantId`, `branchId`
     * and `sourceSystem` (each optional, a string or null). Other keys are ignored. Values are not coerced: a
     * non-string value for a string key, including `type`, raises a `\TypeError`.
     *
     * @param array{
     *     type: string, id?: string|null, name?: string|null, tenantId?: string|null,
     *     branchId?: string|null, sourceSystem?: string|null
     * }|array<string, mixed> $array
     * @return static
     * @throws \InvalidArgumentException If `type` is missing or null, or is not one of the {@see ActorType} values
     *                                   (the underlying `\ValueError` is attached as the previous exception).
     */
    public static function fromArray(array $array): static
    {
        if (!isset($array['type'])) {
            throw new \InvalidArgumentException('Actor type is required');
        }

        try {
            $type = ActorType::from($array['type']);
        } catch (\ValueError $e) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Invalid actor type "%s"; allowed types: %s',
                    $array['type'],
                    \implode(', ', \array_column(ActorType::cases(), 'value'))
                ),
                0,
                $e
            );
        }

        return new self(
            $array['id'] ?? null,
            $type,
            $array['name'] ?? null,
            $array['tenantId'] ?? null,
            $array['branchId'] ?? null,
            $array['sourceSystem'] ?? null,
        );
    }

    /**
     * Convert Actor to array representation.
     *
     * Suitable for serialization in domain events and storage.
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
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

    /**
     * Get the actor's unique identifier.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get the actor type.
     *
     * @return ActorType
     */
    public function getType(): ActorType
    {
        return $this->type;
    }

    /**
     * Get the actor's name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the tenant identifier.
     *
     * @return string|null
     */
    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    /**
     * Get the branch identifier.
     *
     * @return string|null
     */
    public function getBranchId(): ?string
    {
        return $this->branchId;
    }

    /**
     * Get the source system name.
     *
     * @return string|null
     */
    public function getSourceSystem(): ?string
    {
        return $this->sourceSystem;
    }

    /**
     * Check if this is a user actor.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->type === ActorType::User;
    }

    /**
     * Check if this is a system actor.
     *
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->type === ActorType::System;
    }

    /**
     * Check if this is an integration actor.
     *
     * @return bool
     */
    public function isIntegration(): bool
    {
        return $this->type === ActorType::Integration;
    }

    /**
     * Check if this is an unknown actor.
     *
     * @return bool
     */
    public function isUnknown(): bool
    {
        return $this->type === ActorType::Unknown;
    }

    /**
     * Get string representation of the actor.
     *
     * @return string
     */
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
