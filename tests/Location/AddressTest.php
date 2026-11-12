<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Tests\Location;

use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Location\Address;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(Address::class)]
#[UsesClass(ArrayKeys::class)]
#[UsesClass(Literal::class)]
#[UsesTrait(CompareSameValueTrait::class)]
#[UsesTrait(ToStringUsingObjectHashTrait::class)]
final class AddressTest extends TestCase
{
    private const EXPECTED = [
        'company' => 'Acme',
        'addressee' => 'Jane',
        'street1' => '1 Main St',
        'street2' => 'Unit 4',
        'city' => 'Manila',
        'province' => 'NCR',
        'country' => 'PH',
        'zip_code' => '1000',
    ];

    public function testFromArrayPositional(): void
    {
        $address = Address::fromArray(['Acme', 'Jane', '1 Main St', 'Unit 4', 'Manila', 'NCR', 'PH', '1000']);

        self::assertSame(self::EXPECTED, $address->toArray());
    }

    public function testFromArrayPositionalIgnoresSurplusElements(): void
    {
        $address = Address::fromArray(['Acme', 'Jane', '1 Main St', 'Unit 4', 'Manila', 'NCR', 'PH', '1000', 'x']);

        self::assertSame(self::EXPECTED, $address->toArray());
    }

    public function testFromArrayNamedWithSnakeCaseKey(): void
    {
        self::assertSame(self::EXPECTED, Address::fromArray(self::EXPECTED)->toArray());
    }

    public function testFromArrayNamedWithCamelCaseKey(): void
    {
        $input = self::EXPECTED;
        unset($input['zip_code']);
        $input['zipCode'] = '1000';

        self::assertSame(self::EXPECTED, Address::fromArray($input)->toArray());
    }

    public function testFromArrayNamedIgnoresSurplusKeys(): void
    {
        self::assertSame(self::EXPECTED, Address::fromArray(self::EXPECTED + ['phone' => '1'])->toArray());
    }

    public function testBothSpellingsOfTheZipCodeKeyLetTheLaterOneWin(): void
    {
        // Pinned for compatibility.
        $address = Address::fromArray(self::EXPECTED + ['zipCode' => '2000']);
        $reversed = Address::fromArray(['zipCode' => '2000'] + self::EXPECTED);

        self::assertSame('2000', $address->getZipCode()->toNative());
        self::assertSame('1000', $reversed->getZipCode()->toNative());
    }

    public function testKebabCaseAndPascalCaseKeysAreAccepted(): void
    {
        $input = [
            'Company' => 'Acme', 'Addressee' => 'Jane', 'street1' => '1 Main St', 'Street2' => 'Unit 4',
            'City' => 'Manila', 'Province' => 'NCR', 'Country' => 'PH', 'zip-code' => '1000',
        ];

        self::assertSame(self::EXPECTED, Address::fromArray($input)->toArray());
    }

    public function testAllCapsKeysAreNotMatched(): void
    {
        // Pinned for compatibility: 'CITY' converts to 'cITY', which is not 'city'.
        $input = ['CITY' => 'Manila'] + self::EXPECTED;
        unset($input['city']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "city"');

        Address::fromArray($input);
    }

    public function testListWhoseFirstElementIsNullIsStillAList(): void
    {
        // Pinned for compatibility: a null first element does not switch to the keyed form; it casts to ''.
        $address = Address::fromArray([null, 'Jane', '1 Main St', 'Unit 4', 'Manila', 'NCR', 'PH', '1000']);

        self::assertSame(\array_replace(self::EXPECTED, ['company' => '']), $address->toArray());
    }

    public function testListIgnoresNonSequentialAndStringKeys(): void
    {
        $address = Address::fromArray([
            0 => 'Acme', 2 => 'Jane', 3 => '1 Main St', 4 => 'Unit 4', 5 => 'Manila', 6 => 'NCR', 7 => 'PH',
            'last' => '1000',
        ]);

        self::assertSame(self::EXPECTED, $address->toArray());
    }

    public function testNullPartsBecomeEmptyStrings(): void
    {
        $input = \array_replace(self::EXPECTED, ['company' => null, 'city' => null]);

        $expected = \array_replace(self::EXPECTED, ['company' => '', 'city' => '']);

        self::assertSame($expected, Address::fromArray($input)->toArray());
    }

    public function testGetters(): void
    {
        $address = Address::fromArray(self::EXPECTED);

        self::assertSame('Acme', $address->getCompany()->toNative());
        self::assertSame('Jane', $address->getAddressee()->toNative());
        self::assertSame('1 Main St', $address->getStreet1()->toNative());
        self::assertSame('Unit 4', $address->getStreet2()->toNative());
        self::assertSame('Manila', $address->getCity()->toNative());
        self::assertSame('NCR', $address->getProvince()->toNative());
        self::assertSame('PH', $address->getCountry()->toNative());
        self::assertSame('1000', $address->getZipCode()->toNative());
    }

    public function testNotProvidedIsAllEmpty(): void
    {
        $array = Address::notProvided()->toArray();

        self::assertSame(array_fill_keys(array_keys(self::EXPECTED), ''), $array);
    }

    /**
     * @return array<string, array{list<string>}>
     */
    public static function tooShortListProvider(): array
    {
        return [
            'seven elements, one short' => [['a', 'b', 'c', 'd', 'e', 'f', 'g']],
            'three elements' => [['a', 'b', 'c']],
            'one element' => [['a']],
        ];
    }

    /**
     * @param list<string> $input
     */
    #[DataProvider('tooShortListProvider')]
    public function testTooShortListNamesTheArity(array $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Address needs eight arguments: see Address::fromArray().');

        Address::fromArray($input);
    }

    public function testListOfExactlyEightElementsIsEnough(): void
    {
        self::assertSame(self::EXPECTED, Address::fromArray(\array_values(self::EXPECTED))->toArray());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function missingKeyProvider(): array
    {
        $cases = [];
        foreach (\array_keys(self::EXPECTED) as $key) {
            $cases[$key] = [$key];
        }

        return $cases;
    }

    #[DataProvider('missingKeyProvider')]
    public function testEveryMissingKeyIsNamed(string $missing): void
    {
        $input = self::EXPECTED;
        unset($input[$missing]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf(
                'Missing key "%s"; accepted keys: company, addressee, street1, street2, city, province, country, '
                . 'zip_code (or zipCode).',
                $missing
            )
        );

        Address::fromArray($input);
    }

    public function testEmptyArrayNamesTheFirstKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "company"');

        Address::fromArray([]);
    }

    public function testMissingNamedKeyThrowsNamingTheKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "addressee"; accepted keys: company, addressee');

        Address::fromArray(['company' => 'Acme']);
    }

    public function testMissingZipCodeIsReportedAsSnakeCase(): void
    {
        $input = self::EXPECTED;
        unset($input['zip_code']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "zip_code"');

        Address::fromArray($input);
    }

    public function testSameValueAs(): void
    {
        $address = Address::fromArray(self::EXPECTED);

        self::assertTrue($address->sameValueAs(Address::fromArray(self::EXPECTED)));
        self::assertFalse($address->sameValueAs(Address::fromArray(['city' => 'Cebu'] + self::EXPECTED)));
        self::assertFalse($address->sameValueAs(Address::notProvided()));
    }

    public function testToStringUsesObjectHash(): void
    {
        $address = Address::notProvided();

        self::assertStringContainsString(spl_object_hash($address), (string) $address);
    }
}
