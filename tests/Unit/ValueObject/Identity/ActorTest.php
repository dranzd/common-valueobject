<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObject\Identity;

use Dranzd\Common\Domain\ValueObject\Identity\Actor;
use Dranzd\Common\Domain\ValueObject\Identity\ActorType;
use Dranzd\Common\Domain\ValueObject\Tests\Unit\ValueObjectTestCase;

/**
 * Test case for Actor value object.
 */
final class ActorTest extends ValueObjectTestCase
{
    public function testUserFactoryMethod(): void
    {
        $actor = Actor::user(
            id: 'usr-123',
            name: 'John Doe',
            tenantId: 'tenant-1',
            branchId: 'branch-5'
        );

        $this->assertInstanceOf(Actor::class, $actor);
        $this->assertEquals('usr-123', $actor->getId());
        $this->assertEquals(ActorType::User, $actor->getType());
        $this->assertEquals('John Doe', $actor->getName());
        $this->assertEquals('tenant-1', $actor->getTenantId());
        $this->assertEquals('branch-5', $actor->getBranchId());
        $this->assertEquals('Application', $actor->getSourceSystem());
        $this->assertTrue($actor->isUser());
        $this->assertFalse($actor->isSystem());
    }

    public function testUserFactoryMethodWithCustomSourceSystem(): void
    {
        $actor = Actor::user(
            id: 'usr-123',
            name: 'John Doe',
            tenantId: 'tenant-1',
            branchId: null,
            sourceSystem: 'CustomApp'
        );

        $this->assertEquals('CustomApp', $actor->getSourceSystem());
    }

    public function testSystemFactoryMethod(): void
    {
        $actor = Actor::system(
            name: 'AutoReplenishmentJob',
            sourceSystem: 'InventorySystem'
        );

        $this->assertInstanceOf(Actor::class, $actor);
        $this->assertNull($actor->getId());
        $this->assertEquals(ActorType::System, $actor->getType());
        $this->assertEquals('AutoReplenishmentJob', $actor->getName());
        $this->assertNull($actor->getTenantId());
        $this->assertNull($actor->getBranchId());
        $this->assertEquals('InventorySystem', $actor->getSourceSystem());
        $this->assertTrue($actor->isSystem());
        $this->assertFalse($actor->isUser());
    }

    public function testIntegrationFactoryMethod(): void
    {
        $actor = Actor::integration(
            id: 'api-client-456',
            name: 'ExternalAPI',
            sourceSystem: 'PartnerSystem'
        );

        $this->assertInstanceOf(Actor::class, $actor);
        $this->assertEquals('api-client-456', $actor->getId());
        $this->assertEquals(ActorType::Integration, $actor->getType());
        $this->assertEquals('ExternalAPI', $actor->getName());
        $this->assertNull($actor->getTenantId());
        $this->assertNull($actor->getBranchId());
        $this->assertEquals('PartnerSystem', $actor->getSourceSystem());
        $this->assertTrue($actor->isIntegration());
        $this->assertFalse($actor->isUser());
    }

    public function testUnknownFactoryMethod(): void
    {
        $actor = Actor::unknown();

        $this->assertInstanceOf(Actor::class, $actor);
        $this->assertNull($actor->getId());
        $this->assertEquals(ActorType::Unknown, $actor->getType());
        $this->assertNull($actor->getName());
        $this->assertNull($actor->getTenantId());
        $this->assertNull($actor->getBranchId());
        $this->assertNull($actor->getSourceSystem());
        $this->assertTrue($actor->isUnknown());
        $this->assertFalse($actor->isUser());
    }

    public function testFromArrayWithCompleteData(): void
    {
        $data = [
            'id' => 'usr-123',
            'type' => 'User',
            'name' => 'Jane Doe',
            'tenantId' => 'tenant-2',
            'branchId' => 'branch-3',
            'sourceSystem' => 'Application',
        ];

        $actor = Actor::fromArray($data);

        $this->assertEquals('usr-123', $actor->getId());
        $this->assertEquals(ActorType::User, $actor->getType());
        $this->assertEquals('Jane Doe', $actor->getName());
        $this->assertEquals('tenant-2', $actor->getTenantId());
        $this->assertEquals('branch-3', $actor->getBranchId());
        $this->assertEquals('Application', $actor->getSourceSystem());
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'type' => 'System',
            'name' => 'BackgroundJob',
            'sourceSystem' => 'CronSystem',
        ];

        $actor = Actor::fromArray($data);

        $this->assertNull($actor->getId());
        $this->assertEquals(ActorType::System, $actor->getType());
        $this->assertEquals('BackgroundJob', $actor->getName());
        $this->assertNull($actor->getTenantId());
        $this->assertNull($actor->getBranchId());
        $this->assertEquals('CronSystem', $actor->getSourceSystem());
    }

    public function testFromArrayThrowsExceptionForMissingType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Actor type is required');

        Actor::fromArray(['name' => 'Test']);
    }

    public function testFromArrayThrowsExceptionForInvalidType(): void
    {
        $this->expectException(\ValueError::class);

        Actor::fromArray(['type' => 'InvalidType']);
    }

    public function testToArray(): void
    {
        $actor = Actor::user(
            id: 'usr-789',
            name: 'Bob Smith',
            tenantId: 'tenant-3',
            branchId: 'branch-1'
        );

        $array = $actor->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('usr-789', $array['id']);
        $this->assertEquals('User', $array['type']);
        $this->assertEquals('Bob Smith', $array['name']);
        $this->assertEquals('tenant-3', $array['tenantId']);
        $this->assertEquals('branch-1', $array['branchId']);
        $this->assertEquals('Application', $array['sourceSystem']);
    }

    public function testToArrayWithNullValues(): void
    {
        $actor = Actor::system('TestJob', 'TestSystem');

        $array = $actor->toArray();

        $this->assertNull($array['id']);
        $this->assertNull($array['tenantId']);
        $this->assertNull($array['branchId']);
    }

    public function testSerializationRoundTrip(): void
    {
        $original = Actor::user(
            id: 'usr-999',
            name: 'Alice Johnson',
            tenantId: 'tenant-4',
            branchId: 'branch-2'
        );

        $array = $original->toArray();
        $reconstructed = Actor::fromArray($array);

        $this->assertValueObjectEquals($original, $reconstructed);
    }

    public function testJsonSerialization(): void
    {
        $actor = Actor::integration(
            id: 'api-789',
            name: 'PartnerAPI',
            sourceSystem: 'External'
        );

        $this->assertJsonSerializable($actor);
    }

    public function testEqualityWithSameValues(): void
    {
        $actor1 = Actor::user('usr-1', 'Test User', 'tenant-1', 'branch-1');
        $actor2 = Actor::user('usr-1', 'Test User', 'tenant-1', 'branch-1');

        $this->assertValueObjectEquals($actor1, $actor2);
    }

    public function testInequalityWithDifferentIds(): void
    {
        $actor1 = Actor::user('usr-1', 'Test User', 'tenant-1');
        $actor2 = Actor::user('usr-2', 'Test User', 'tenant-1');

        $this->assertValueObjectNotEquals($actor1, $actor2);
    }

    public function testInequalityWithDifferentTypes(): void
    {
        $actor1 = Actor::user('usr-1', 'Test', 'tenant-1');
        $actor2 = Actor::system('Test', 'System');

        $this->assertValueObjectNotEquals($actor1, $actor2);
    }

    public function testInequalityWithDifferentNames(): void
    {
        $actor1 = Actor::system('Job1', 'System');
        $actor2 = Actor::system('Job2', 'System');

        $this->assertValueObjectNotEquals($actor1, $actor2);
    }

    public function testToString(): void
    {
        $actor = Actor::user('usr-123', 'John Doe', 'tenant-1');

        $string = (string) $actor;

        $this->assertEquals('User: John Doe (usr-123)', $string);
    }

    public function testToStringWithNullValues(): void
    {
        $actor = Actor::unknown();

        $string = (string) $actor;

        $this->assertEquals('Unknown: Unknown (N/A)', $string);
    }

    public function testImmutability(): void
    {
        $actor = Actor::user('usr-1', 'Test', 'tenant-1');

        $this->assertImmutable($actor);
    }

    public function testIsUserHelper(): void
    {
        $userActor = Actor::user('usr-1', 'Test', 'tenant-1');
        $systemActor = Actor::system('Job', 'System');

        $this->assertTrue($userActor->isUser());
        $this->assertFalse($systemActor->isUser());
    }

    public function testIsSystemHelper(): void
    {
        $userActor = Actor::user('usr-1', 'Test', 'tenant-1');
        $systemActor = Actor::system('Job', 'System');

        $this->assertFalse($userActor->isSystem());
        $this->assertTrue($systemActor->isSystem());
    }

    public function testIsIntegrationHelper(): void
    {
        $integrationActor = Actor::integration('api-1', 'API', 'External');
        $userActor = Actor::user('usr-1', 'Test', 'tenant-1');

        $this->assertTrue($integrationActor->isIntegration());
        $this->assertFalse($userActor->isIntegration());
    }

    public function testIsUnknownHelper(): void
    {
        $unknownActor = Actor::unknown();
        $userActor = Actor::user('usr-1', 'Test', 'tenant-1');

        $this->assertTrue($unknownActor->isUnknown());
        $this->assertFalse($userActor->isUnknown());
    }

    public function testAllGetters(): void
    {
        $actor = Actor::user(
            id: 'usr-complete',
            name: 'Complete User',
            tenantId: 'tenant-complete',
            branchId: 'branch-complete',
            sourceSystem: 'CompleteSystem'
        );

        $this->assertEquals('usr-complete', $actor->getId());
        $this->assertEquals(ActorType::User, $actor->getType());
        $this->assertEquals('Complete User', $actor->getName());
        $this->assertEquals('tenant-complete', $actor->getTenantId());
        $this->assertEquals('branch-complete', $actor->getBranchId());
        $this->assertEquals('CompleteSystem', $actor->getSourceSystem());
    }

    /**
     * Test that Actor can be used in domain events.
     */
    public function testUsageInDomainEvent(): void
    {
        $actor = Actor::user('usr-123', 'Jane Doe', 'tenant-1', 'branch-3');

        $eventData = [
            'event' => 'StockConsumed',
            'itemId' => 'ITEM-001',
            'quantity' => 5,
            'consumedBy' => $actor->toArray(),
            'timestamp' => '2025-11-13T00:00:00Z',
        ];

        $this->assertIsArray($eventData['consumedBy']);
        $this->assertEquals('usr-123', $eventData['consumedBy']['id']);
        $this->assertEquals('User', $eventData['consumedBy']['type']);

        // Reconstruct from event
        $reconstructedActor = Actor::fromArray($eventData['consumedBy']);
        $this->assertValueObjectEquals($actor, $reconstructedActor);
    }
}
