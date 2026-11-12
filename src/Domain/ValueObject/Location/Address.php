<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Location;

use Dranzd\Common\Domain\ValueObject\ArrayValueObjectTranslatorCapable;
use Dranzd\Common\Domain\ValueObject\CompareSameValueTrait;
use Dranzd\Common\Domain\ValueObject\Internal\ArrayKeys;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Addressee;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as City;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Company;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Country;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Province;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as Street;
use Dranzd\Common\Domain\ValueObject\Native\String\Literal as ZipCode;
use Dranzd\Common\Domain\ValueObject\ToStringUsingObjectHashTrait;
use Dranzd\Common\Domain\ValueObject\ValueObject;

/**
 * A postal address made of eight free-form string parts, none of which is validated.
 *
 * Instances are immutable. Keys are `company`, `addressee`, `street1`, `street2`, `city`, `province`, `country`
 * and `zip_code` (`zipCode` is accepted on input).
 */
final class Address implements ValueObject, ArrayValueObjectTranslatorCapable
{
    use CompareSameValueTrait;
    use ToStringUsingObjectHashTrait;

    private Company $company;

    private Addressee $addressee;

    private Street $street1;

    private Street $street2;

    private City $city;

    private Province $province;

    private Country $country;

    private ZipCode $zipCode;

    private function __construct(
        Company $company,
        Addressee $addressee,
        Street $street1,
        Street $street2,
        City $city,
        Province $province,
        Country $country,
        ZipCode $zipCode
    ) {
        $this->company = $company;
        $this->addressee = $addressee;
        $this->street1 = $street1;
        $this->street2 = $street2;
        $this->city = $city;
        $this->province = $province;
        $this->country = $country;
        $this->zipCode = $zipCode;
    }

    /**
     * An address whose eight parts are empty strings.
     */
    final public static function notProvided(): Address
    {
        return self::fromArray(['', '', '', '', '', '', '', '']);
    }

    /**
     * Build from a positional list in the order company, addressee, street1, street2, city, province, country,
     * zip code, or from an array with the keys of {@see self::toArray()} (`zipCode` works as well as `zip_code`).
     *
     * Every part is cast with `(string)`. Surplus elements and keys are ignored. The list form is used whenever
     * the array has an element `0`, even a null one; its values are taken in order, whatever their keys. Keys are
     * matched after camelCase conversion, so `Company` and `zipCode` work too. When two keys convert to the same
     * name the later one wins: `['zip_code' => '1000', 'zipCode' => '2000']` gives `2000`.
     *
     * @param array<array-key, mixed> $array
     * @throws \InvalidArgumentException If an array with keys lacks one of the eight keys, or if a list has fewer
     *                                   than eight elements.
     */
    final public static function fromArray(array $array): Address
    {
        if (\array_key_exists(0, $array)) {
            return self::fromNative(...\array_values($array));
        }

        $data = ArrayKeys::toCamelCase($array);
        foreach (['company', 'addressee', 'street1', 'street2', 'city', 'province', 'country', 'zipCode'] as $key) {
            if (!\array_key_exists($key, $data)) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'Missing key "%s"; accepted keys: company, addressee, street1, street2, city, province, '
                        . 'country, zip_code (or zipCode).',
                        $key === 'zipCode' ? 'zip_code' : $key
                    )
                );
            }
        }

        return self::fromNative(
            $data['company'],
            $data['addressee'],
            $data['street1'],
            $data['street2'],
            $data['city'],
            $data['province'],
            $data['country'],
            $data['zipCode']
        );
    }

    final public function sameValueAs(ValueObject $object): bool
    {
        return $this->checkSameValueAs($object);
    }

    /**
     * @return array{
     *     company: string, addressee: string, street1: string, street2: string,
     *     city: string, province: string, country: string, zip_code: string
     * }
     */
    final public function toArray(): array
    {
        return [
            'company' => $this->company->toNative(),
            'addressee' => $this->addressee->toNative(),
            'street1' => $this->street1->toNative(),
            'street2' => $this->street2->toNative(),
            'city' => $this->city->toNative(),
            'province' => $this->province->toNative(),
            'country' => $this->country->toNative(),
            'zip_code' => $this->zipCode->toNative(),
        ];
    }

    final public function getCompany(): Company
    {
        return $this->company;
    }

    final public function getAddressee(): Addressee
    {
        return $this->addressee;
    }

    final public function getStreet1(): Street
    {
        return $this->street1;
    }

    final public function getStreet2(): Street
    {
        return $this->street2;
    }

    final public function getCity(): City
    {
        return $this->city;
    }

    final public function getProvince(): Province
    {
        return $this->province;
    }

    final public function getCountry(): Country
    {
        return $this->country;
    }

    final public function getZipCode(): ZipCode
    {
        return $this->zipCode;
    }

    private static function fromNative(): Address
    {
        if (\func_num_args() < 8) {
            throw new \InvalidArgumentException('Address needs eight arguments: see Address::fromArray().');
        }

        $company = (string) \func_get_arg(0);
        $addressee = (string) \func_get_arg(1);
        $street1 = (string) \func_get_arg(2);
        $street2 = (string) \func_get_arg(3);
        $city = (string) \func_get_arg(4);
        $province = (string) \func_get_arg(5);
        $country = (string) \func_get_arg(6);
        $zipCode = (string) \func_get_arg(7);

        $company = Company::fromNative($company);
        $addressee = Addressee::fromNative($addressee);
        $street1 = Street::fromNative($street1);
        $street2 = Street::fromNative($street2);
        $city = City::fromNative($city);
        $province = Province::fromNative($province);
        $country = Country::fromNative($country);
        $zipCode = ZipCode::fromNative($zipCode);

        return new self(
            $company,
            $addressee,
            $street1,
            $street2,
            $city,
            $province,
            $country,
            $zipCode
        );
    }
}
