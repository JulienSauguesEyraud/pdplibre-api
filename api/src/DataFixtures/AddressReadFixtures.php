<?php

namespace App\DataFixtures;

use App\Directory\Doctrine\Entity\AddressRead;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class AddressReadFixtures extends ArrayFixture implements ORMFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function getEntityClass(): string
    {
        return AddressRead::class;
    }

    public function getReferencePrefix(): ?string
    {
        return 'address-';
    }

    public function getObjects(): iterable
    {
        yield [
            'id' => 1,
            'addressLine1' => 'address 1',
            'addressLine2' => 'address 2',
            'addressLine3' => 'address 3',
            'postalCode' => '12345',
            'countrySubdivision' => "subdivision",
            'locality' => 'locality',
            'countryCode' => 'FR',
            'countryName' => 'France',
        ];

        for ($i = 2; $i <= 1000; ++$i) {
            $countryCode = 'FR';
            $countryName = 'France';
            if (10 === random_int(1, 10)) {
                $countryCode = $this->faker->countryCode();
                $countryName = $this->faker->country();
            }
            yield [
                'id' => $i,
                'addressLine1' => $this->faker->streetAddress(),
                'addressLine2' => 'CEDEX'.random_int(1, 10),
                'addressLine3' => $this->faker->secondaryAddress(),
                'postalCode' => (string) $this->faker->randomNumber(5, true),
                'countrySubdivision' => $this->faker->region(),
                'locality' => $this->faker->city(),
                'countryCode' => $countryCode,
                'countryName' => $countryName,
            ];
        }
    }
}
