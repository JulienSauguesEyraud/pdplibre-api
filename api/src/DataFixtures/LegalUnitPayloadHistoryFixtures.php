<?php

namespace App\DataFixtures;

use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class LegalUnitPayloadHistoryFixtures extends ArrayFixture implements ORMFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function getEntityClass(): string
    {
        return LegalUnitPayloadHistory::class;
    }

    public function getReferencePrefix(): ?string
    {
        return 'legalUnit-';
    }

    public function getObjects(): iterable
    {
        $idInstance = 1;
        $id = 1;
        for ($i = 1; $i <= 300; ++$i) {
            $siren = (string) $this->faker->randomNumber(9, true);
            $businessName = $this->faker->company();
            $entityType = $this->faker->randomElement(EntityType::class);
            $administrativeStatus = LegalUnitAdministrativeStatus::A;
            $nbVersions = random_int(1, 2);

            for ($v = 1; $v <= $nbVersions; ++$v) {
                if (2 === $v) {
                    $administrativeStatus = LegalUnitAdministrativeStatus::C;
                }
                yield [
                    'id' => $id,
                    'idInstance' => $idInstance,
                    'siren' => $siren,
                    'businessName' => $businessName,
                    'entityType' => $entityType,
                    'administrativeStatus' => $administrativeStatus,
                    'version' => $v,
                    'updatedAt' => new \DateTimeImmutable()->modify('-'.($nbVersions - $v).' years'),
                ];

                ++$id;
            }
            ++$idInstance;
        }
    }
}
