<?php

namespace App\DataFixtures;

use App\Directory\Doctrine\Entity\B2gAdditionalData;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\ArrayFixture\ArrayFixture;

final class B2gAdditionalDataFixtures extends ArrayFixture implements ORMFixtureInterface
{
    public function getEntityClass(): string
    {
        return B2gAdditionalData::class;
    }

    public function getReferencePrefix(): ?string
    {
        return 'b2gAdditionalData-';
    }

    public function getObjects(): iterable
    {
        yield [
            'id' => 1,
            'pm' => true,
            'pmOnly' => true,
            'managesPaymentStatus' => true,
            'managesLegalCommitmentCode' => true,
            'managesLegalCommitmentOrServiceCode' => true,
            'serviceCodeStatus' => true,
        ];

        for ($i = 2; $i <= 1000; ++$i) {
            yield [
                'id' => $i,
                'pm' => 1 === random_int(0, 1),
                'pmOnly' => 1 === random_int(0, 1),
                'managesPaymentStatus' => 1 === random_int(0, 1),
                'managesLegalCommitmentCode' => 1 === random_int(0, 1),
                'managesLegalCommitmentOrServiceCode' => 1 === random_int(0, 1),
                'serviceCodeStatus' => 1 === random_int(0, 1),
            ];
        }
    }
}
