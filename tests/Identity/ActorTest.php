<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Identity;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Identity\Actor;
use Dranzd\Common\Domain\ValueObject\Identity\ActorType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Actor::class)]
#[UsesTrait(CompareSameValueTrait::class)]
final class ActorTest extends TestCase
{
    public function testUserFactory(): void
    {
        $actor = Actor::user(id: 'usr-123', name: 'John Doe', tenantId: 'tenant-1', branchId: 'branch-5');

        self::assertSame('usr-123', $actor->getId());
        self::assertSame(ActorType::User, $actor->getType());
        self::assertSame('John Doe', $actor->getName());
        self::assertSame('tenant-1', $actor->getTenantId());
        self::assertSame('branch-5', $actor->getBranchId());
        self::assertSame('Application', $actor->getSourceSystem());
        self::assertTrue($actor->isUser());
        self::assertFalse($actor->isSystem());
        self::assertFalse($actor->isIntegration());
        self::assertFalse($actor->isUnknown());
    }

    public function testUserFactoryWithCustomSourceSystemAndNoBranch(): void
    {
        $actor = Actor::user(
            id: 'usr-123',
            name: 'John Doe',
            tenantId: 'tenant-1',
            branchId: null,
            sourceSystem: 'CustomApp'
        );

        self::assertNull($actor->getBranchId());
        self::assertSame('CustomApp', $actor->getSourceSystem());
    }

    public function testUserFactoryWithNullSourceSystem(): void
    {
        self::assertNull(Actor::user('u', 'n', 't', null, null)->getSourceSystem());
    }

    public function testSystemFactory(): void
    {
        $actor = Actor::system(name: 'NightlyInvoiceRun', sourceSystem: 'Billing');

        self::assertNull($actor->getId());
        self::assertSame(ActorType::System, $actor->getType());
        self::assertSame('NightlyInvoiceRun', $actor->getName());
        self::assertNull($actor->getTenantId());
        self::assertNull($actor->getBranchId());
        self::assertSame('Billing', $actor->getSourceSystem());
        self::assertTrue($actor->isSystem());
        self::assertFalse($actor->isUser());
        self::assertFalse($actor->isIntegration());
        self::assertFalse($actor->isUnknown());
    }

    public function testIntegrationFactory(): void
    {
        $actor = Actor::integration(id: 'api-client-456', name: 'ExternalAPI', sourceSystem: 'PartnerSystem');

        self::assertSame('api-client-456', $actor->getId());
        self::assertSame(ActorType::Integration, $actor->getType());
        self::assertSame('ExternalAPI', $actor->getName());
        self::assertNull($actor->getTenantId());
        self::assertNull($actor->getBranchId());
        self::assertSame('PartnerSystem', $actor->getSourceSystem());
        self::assertTrue($actor->isIntegration());
        self::assertFalse($actor->isUser());
        self::assertFalse($actor->isSystem());
        self::assertFalse($actor->isUnknown());
    }

    public function testUnknownFactory(): void
    {
        $actor = Actor::unknown();

        self::assertNull($actor->getId());
        self::assertSame(ActorType::Unknown, $actor->getType());
        self::assertNull($actor->getName());
        self::assertNull($actor->getTenantId());
        self::assertNull($actor->getBranchId());
        self::assertNull($actor->getSourceSystem());
        self::assertTrue($actor->isUnknown());
        self::assertFalse($actor->isUser());
        self::assertFalse($actor->isSystem());
        self::assertFalse($actor->isIntegration());
    }

    public function testFromArrayWithCompleteData(): void
    {
        $actor = Actor::fromArray([
            'id' => 'usr-123',
            'type' => 'User',
            'name' => 'Jane Doe',
            'tenantId' => 'tenant-2',
            'branchId' => 'branch-3',
            'sourceSystem' => 'Application',
        ]);

        self::assertSame('usr-123', $actor->getId());
        self::assertSame(ActorType::User, $actor->getType());
        self::assertSame('Jane Doe', $actor->getName());
        self::assertSame('tenant-2', $actor->getTenantId());
        self::assertSame('branch-3', $actor->getBranchId());
        self::assertSame('Application', $actor->getSourceSystem());
    }

    public function testFromArrayWithMinimalData(): void
    {
        $actor = Actor::fromArray(['type' => 'System', 'name' => 'BackgroundJob', 'sourceSystem' => 'CronSystem']);

        self::assertNull($actor->getId());
        self::assertSame(ActorType::System, $actor->getType());
        self::assertSame('BackgroundJob', $actor->getName());
        self::assertNull($actor->getTenantId());
        self::assertNull($actor->getBranchId());
        self::assertSame('CronSystem', $actor->getSourceSystem());
    }

    public function testFromArrayAcceptsExplicitNullsAndIgnoresUnknownKeys(): void
    {
        $actor = Actor::fromArray(['type' => 'Unknown', 'id' => null, 'name' => null, 'extra' => 1]);

        self::assertTrue($actor->sameValueAs(Actor::unknown()));
    }

    public function testFromArrayWithoutTypeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Actor type is required');

        Actor::fromArray(['name' => 'Test']);
    }

    public function testFromArrayWithNullTypeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Actor type is required');

        Actor::fromArray(['type' => null]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidTypeProvider(): array
    {
        return [
            'unknown name' => ['InvalidType'],
            'wrong case' => ['user'],
            'empty string' => [''],
        ];
    }

    #[DataProvider('invalidTypeProvider')]
    public function testFromArrayWithInvalidTypeThrowsInvalidArgumentWithAllowedValues(string $type): void
    {
        try {
            Actor::fromArray(['type' => $type]);
            self::fail('Expected an InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            self::assertStringContainsString(sprintf('Invalid actor type "%s"', $type), $e->getMessage());
            self::assertStringContainsString('allowed types: User, System, Integration, Unknown', $e->getMessage());
            self::assertSame(0, $e->getCode());
            self::assertInstanceOf(\ValueError::class, $e->getPrevious());
        }
    }

    public function testFromArrayWithNonStringTypeStaysATypeError(): void
    {
        $this->expectException(\TypeError::class);

        Actor::fromArray(['type' => 5]);
    }

    public function testFromArrayWithNonStringIdStaysATypeError(): void
    {
        $this->expectException(\TypeError::class);

        Actor::fromArray(['type' => 'User', 'id' => 5]);
    }

    public function testFromArrayWithNonStringNameStaysATypeError(): void
    {
        $this->expectException(\TypeError::class);

        Actor::fromArray(['type' => 'User', 'name' => 5]);
    }

    public function testToArray(): void
    {
        $actor = Actor::user(id: 'usr-789', name: 'Bob Smith', tenantId: 'tenant-3', branchId: 'branch-1');

        self::assertSame(
            [
                'id' => 'usr-789',
                'type' => 'User',
                'name' => 'Bob Smith',
                'tenantId' => 'tenant-3',
                'branchId' => 'branch-1',
                'sourceSystem' => 'Application',
            ],
            $actor->toArray()
        );
    }

    public function testToArrayWithNullValues(): void
    {
        self::assertSame(
            [
                'id' => null,
                'type' => 'System',
                'name' => 'TestJob',
                'tenantId' => null,
                'branchId' => null,
                'sourceSystem' => 'TestSystem',
            ],
            Actor::system('TestJob', 'TestSystem')->toArray()
        );
    }

    /**
     * @return array<string, array{Actor}>
     */
    public static function actorProvider(): array
    {
        return [
            'user' => [Actor::user('usr-999', 'Alice Johnson', 'tenant-4', 'branch-2')],
            'system' => [Actor::system('Job', 'System')],
            'integration' => [Actor::integration('api-789', 'PartnerAPI', 'External')],
            'unknown' => [Actor::unknown()],
        ];
    }

    #[DataProvider('actorProvider')]
    public function testArrayAndJsonRoundTrip(Actor $original): void
    {
        self::assertTrue($original->sameValueAs(Actor::fromArray($original->toArray())));

        $decoded = json_decode(json_encode($original->toArray(), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);
        self::assertTrue($original->sameValueAs(Actor::fromArray($decoded)));
    }

    public function testSameValueAs(): void
    {
        $actor = Actor::user('usr-1', 'Test User', 'tenant-1', 'branch-1');

        self::assertTrue($actor->sameValueAs(Actor::user('usr-1', 'Test User', 'tenant-1', 'branch-1')));
        self::assertFalse($actor->sameValueAs(Actor::user('usr-2', 'Test User', 'tenant-1', 'branch-1')));
        self::assertFalse($actor->sameValueAs(Actor::user('usr-1', 'Other', 'tenant-1', 'branch-1')));
        self::assertFalse($actor->sameValueAs(Actor::user('usr-1', 'Test User', 'tenant-2', 'branch-1')));
        self::assertFalse($actor->sameValueAs(Actor::user('usr-1', 'Test User', 'tenant-1', 'branch-2')));
        self::assertFalse($actor->sameValueAs(Actor::user('usr-1', 'Test User', 'tenant-1', 'branch-1', 'Other')));
        self::assertFalse(Actor::system('Job1', 'System')->sameValueAs(Actor::system('Job2', 'System')));
        self::assertFalse(Actor::user('x', 'Test', 't')->sameValueAs(Actor::integration('x', 'Test', 'Application')));
    }

    public function testToString(): void
    {
        self::assertSame('User: John Doe (usr-123)', (string) Actor::user('usr-123', 'John Doe', 'tenant-1'));
        self::assertSame('Unknown: Unknown (N/A)', (string) Actor::unknown());
    }

    public function testActorExposesNoMutators(): void
    {
        $reflection = new \ReflectionClass(Actor::class);

        self::assertTrue($reflection->isFinal());
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            self::assertStringStartsNotWith('set', $method->getName());
        }
        foreach ($reflection->getProperties() as $property) {
            self::assertTrue($property->isReadOnly());
            self::assertFalse($property->isPublic());
        }
    }
}
