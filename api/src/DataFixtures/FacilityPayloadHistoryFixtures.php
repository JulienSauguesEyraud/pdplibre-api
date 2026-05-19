<?php

namespace App\DataFixtures;

use App\Directory\Doctrine\Entity\AddressRead;
use App\Directory\Doctrine\Entity\B2gAdditionalData;
use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class FacilityPayloadHistoryFixtures extends ArrayFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function getEntityClass(): string
    {
        return FacilityPayloadHistory::class;
    }

    public function getDependencies(): array
    {
        return [
            LegalUnitPayloadHistoryFixtures::class,
            AddressReadFixtures::class,
            B2gAdditionalDataFixtures::class,
        ];
    }

    public function getObjects(): iterable
    {
        yield [
            'id' => 1,
            'idInstance' => 1,
            'siren' => '123456789',
            'siret' => '12345678900000',
            'name' => 'test name',
            'facilityType' => FacilityType::P,
            'diffusible' => DiffusionStatus::P,
            'administrativeStatus' => FacilityAdministrativeStatus::C,
            'address' => $this->getReference('address-1', AddressRead::class),
            'b2gAdditionalData' => $this->getReference('b2gAdditionalData-1', B2gAdditionalData::class),
            'legalUnit' => $this->getReference('legalUnit-1', LegalUnitPayloadHistory::class),
            'version' => 1,
            'updatedAt' => new \DateTimeImmutable()->modify('-2 years'),
        ];

        yield [
            'id' => 2,
            'idInstance' => 1,
            'siren' => '123456789',
            'siret' => '12345678900000',
            'name' => 'test name',
            'facilityType' => FacilityType::P,
            'diffusible' => DiffusionStatus::P,
            'administrativeStatus' => FacilityAdministrativeStatus::A,
            'address' => $this->getReference('address-1', AddressRead::class),
            'b2gAdditionalData' => $this->getReference('b2gAdditionalData-1', B2gAdditionalData::class),
            'legalUnit' => $this->getReference('legalUnit-1', LegalUnitPayloadHistory::class),
            'version' => 2,
            'updatedAt' => new \DateTimeImmutable()->modify('-1 years'),
        ];

        yield [
            'id' => 3,
            'idInstance' => 1,
            'siren' => '123456789',
            'siret' => '12345678900000',
            'name' => 'test name',
            'facilityType' => FacilityType::P,
            'diffusible' => DiffusionStatus::P,
            'administrativeStatus' => FacilityAdministrativeStatus::A,
            'address' => $this->getReference('address-1', AddressRead::class),
            'b2gAdditionalData' => $this->getReference('b2gAdditionalData-1', B2gAdditionalData::class),
            'legalUnit' => $this->getReference('legalUnit-2', LegalUnitPayloadHistory::class),
            'version' => 3,
            'updatedAt' => new \DateTimeImmutable(),
        ];

        $id = 4;
        $idInstance = 2;

        for ($i = 2; $i <= 1000; ++$i) {
            $legalUnit = $this->getReference('legalUnit-'.random_int(1, 300), LegalUnitPayloadHistory::class);
            $siren = $legalUnit->getSiren();
            $siret = $siren.$this->faker->randomNumber(5, true);
            $name = $legalUnit->getBusinessName().' '.$this->faker->jobTitle();
            $facilityType = $this->faker->randomElement(FacilityType::class);
            $diffusible = $this->faker->randomElement(DiffusionStatus::class);
            $administrativeStatus = FacilityAdministrativeStatus::A;
            $nbVersions = random_int(1, 2);

            for ($v = 1; $v <= $nbVersions; ++$v) {
                if (2 === $v) {
                    $administrativeStatus = FacilityAdministrativeStatus::C;
                }
                yield [
                    'id' => $id,
                    'idInstance' => $idInstance,
                    'siren' => $siren,
                    'siret' => $siret,
                    'name' => $name,
                    'facilityType' => $facilityType,
                    'diffusible' => $diffusible,
                    'administrativeStatus' => $administrativeStatus,
                    'address' => $this->getReference('address-'.$i, AddressRead::class),
                    'b2gAdditionalData' => $this->getReference('b2gAdditionalData-'.$i, B2gAdditionalData::class),
                    'legalUnit' => $legalUnit,
                    'version' => $v,
                    'updatedAt' => new \DateTimeImmutable()->modify('-'.($nbVersions - $v).' years'),
                ];

                ++$id;
            }

            ++$idInstance;
        }
    }
}
