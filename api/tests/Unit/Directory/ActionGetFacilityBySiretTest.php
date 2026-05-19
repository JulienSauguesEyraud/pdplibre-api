<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Actions\GetFacilityBySiret;
use App\Directory\Doctrine\Entity\AddressRead;
use App\Directory\Doctrine\Entity\B2gAdditionalData;
use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use App\Directory\Repository\FacilityPayloadHistoryRepository;
use PHPUnit\Framework\TestCase;

final class ActionGetFacilityBySiretTest extends TestCase
{
    public function testReturnsOutput(): void
    {
        $addressEntity = $this->createMock(AddressRead::class);

        $addressEntity->method('getAddressLine1')->willReturn('address 1');
        $addressEntity->method('getAddressLine2')->willReturn('address 2');
        $addressEntity->method('getAddressLine3')->willReturn('address 3');
        $addressEntity->method('getPostalCode')->willReturn('12345');
        $addressEntity->method('getCountrySubdivision')->willReturn('subdivision');
        $addressEntity->method('getLocality')->willReturn('locality');
        $addressEntity->method('getCountryCode')->willReturn('FR');
        $addressEntity->method('getCountryName')->willReturn('France');

        $b2gAdditionalDataEntity = $this->createMock(B2gAdditionalData::class);

        $b2gAdditionalDataEntity->method('getPm')->willReturn(true);
        $b2gAdditionalDataEntity->method('getPmOnly')->willReturn(true);
        $b2gAdditionalDataEntity->method('getManagesPaymentStatus')->willReturn(true);
        $b2gAdditionalDataEntity->method('getManagesLegalCommitmentCode')->willReturn(true);
        $b2gAdditionalDataEntity->method('getManagesLegalCommitmentOrServiceCode')->willReturn(true);
        $b2gAdditionalDataEntity->method('getServiceCodeStatus')->willReturn(true);

        $legalUnitEntity = $this->createMock(LegalUnitPayloadHistory::class);

        $legalUnitEntity->method('getBusinessName')->willReturn('test business name');
        $legalUnitEntity->method('getEntityType')->willReturn(EntityType::Public);
        $legalUnitEntity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $entity = $this->createMock(FacilityPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getSiret')->willReturn('12345678900000');
        $entity->method('getName')->willReturn('test name');
        $entity->method('getFacilityType')->willReturn(FacilityType::P);
        $entity->method('getDiffusible')->willReturn(DiffusionStatus::P);
        $entity->method('getAdministrativeStatus')->willReturn(FacilityAdministrativeStatus::A);

        $entity->method('getAddress')->willReturn($addressEntity);
        $entity->method('getB2gAdditionalData')->willReturn($b2gAdditionalDataEntity);
        $entity->method('getLegalUnit')->willReturn($legalUnitEntity);

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('getFacilityBySiret')
            ->with('12345678900000')
            ->willReturn($entity);

        $action = new GetFacilityBySiret($repository);

        $result = $action->__invoke('12345678900000');

        self::assertSame(1, $result->idInstance);
        self::assertSame('123456789', $result->siren);
        self::assertSame('12345678900000', $result->siret);
        self::assertSame('test name', $result->name);
        self::assertSame(FacilityType::P, $result->facilityType);
        self::assertSame(DiffusionStatus::P, $result->diffusible);
        self::assertSame(FacilityAdministrativeStatus::A, $result->administrativeStatus);

        self::assertSame('address 1', $result->address->addressLine1);
        self::assertSame('address 2', $result->address->addressLine2);
        self::assertSame('address 3', $result->address->addressLine3);
        self::assertSame('12345', $result->address->postalCode);
        self::assertSame('subdivision', $result->address->countrySubdivision);
        self::assertSame('locality', $result->address->locality);
        self::assertSame('FR', $result->address->countryCode);
        self::assertSame('France', $result->address->countryName);

        self::assertSame(true, $result->b2gAdditionalData->pm);
        self::assertSame(true, $result->b2gAdditionalData->pmOnly);
        self::assertSame(true, $result->b2gAdditionalData->managesPaymentStatus);
        self::assertSame(true, $result->b2gAdditionalData->managesLegalCommitmentCode);
        self::assertSame(true, $result->b2gAdditionalData->managesLegalCommitmentOrServiceCode);
        self::assertSame(true, $result->b2gAdditionalData->serviceCodeStatus);

        self::assertSame('test business name', $result->legalUnit->businessName);
        self::assertSame(EntityType::Public, $result->legalUnit->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->legalUnit->administrativeStatus);
    }

    public function testReturnsOutputWithField(): void
    {
        $addressEntity = $this->createMock(AddressRead::class);

        $addressEntity->method('getAddressLine1')->willReturn('address 1');
        $addressEntity->method('getAddressLine2')->willReturn('address 2');
        $addressEntity->method('getAddressLine3')->willReturn('address 3');
        $addressEntity->method('getPostalCode')->willReturn('12345');
        $addressEntity->method('getCountrySubdivision')->willReturn('subdivision');
        $addressEntity->method('getLocality')->willReturn('locality');
        $addressEntity->method('getCountryCode')->willReturn('FR');
        $addressEntity->method('getCountryName')->willReturn('France');

        $b2gAdditionalDataEntity = $this->createMock(B2gAdditionalData::class);

        $b2gAdditionalDataEntity->method('getPm')->willReturn(true);
        $b2gAdditionalDataEntity->method('getPmOnly')->willReturn(true);
        $b2gAdditionalDataEntity->method('getManagesPaymentStatus')->willReturn(true);
        $b2gAdditionalDataEntity->method('getManagesLegalCommitmentCode')->willReturn(true);
        $b2gAdditionalDataEntity->method('getManagesLegalCommitmentOrServiceCode')->willReturn(true);
        $b2gAdditionalDataEntity->method('getServiceCodeStatus')->willReturn(true);

        $legalUnitEntity = $this->createMock(LegalUnitPayloadHistory::class);

        $legalUnitEntity->method('getBusinessName')->willReturn('test business name');
        $legalUnitEntity->method('getEntityType')->willReturn(EntityType::Public);
        $legalUnitEntity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $entity = $this->createMock(FacilityPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getSiret')->willReturn('12345678900000');
        $entity->method('getName')->willReturn('test name');
        $entity->method('getFacilityType')->willReturn(FacilityType::P);
        $entity->method('getDiffusible')->willReturn(DiffusionStatus::P);
        $entity->method('getAdministrativeStatus')->willReturn(FacilityAdministrativeStatus::A);

        $entity->method('getAddress')->willReturn($addressEntity);
        $entity->method('getB2gAdditionalData')->willReturn($b2gAdditionalDataEntity);
        $entity->method('getLegalUnit')->willReturn($legalUnitEntity);

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('getFacilityBySiret')
            ->with('12345678900000')
            ->willReturn($entity);

        $action = new GetFacilityBySiret($repository);

        $result = $action->__invoke('12345678900000', ['siret', 'name', 'facilityType', 'address', 'pmOnly']);

        self::assertNull($result->idInstance);
        self::assertNull($result->siren);
        self::assertSame('12345678900000', $result->siret);
        self::assertSame('test name', $result->name);
        self::assertSame(FacilityType::P, $result->facilityType);
        self::assertNull($result->diffusible);
        self::assertNull($result->administrativeStatus);

        self::assertSame('address 1', $result->address->addressLine1);
        self::assertSame('address 2', $result->address->addressLine2);
        self::assertSame('address 3', $result->address->addressLine3);
        self::assertSame('12345', $result->address->postalCode);
        self::assertSame('subdivision', $result->address->countrySubdivision);
        self::assertSame('locality', $result->address->locality);
        self::assertSame('FR', $result->address->countryCode);
        self::assertSame('France', $result->address->countryName);

        self::assertNull($result->b2gAdditionalData->pm);
        self::assertSame(true, $result->b2gAdditionalData->pmOnly);
        self::assertNull($result->b2gAdditionalData->managesPaymentStatus);
        self::assertNull($result->b2gAdditionalData->managesLegalCommitmentCode);
        self::assertNull($result->b2gAdditionalData->managesLegalCommitmentOrServiceCode);
        self::assertNull($result->b2gAdditionalData->serviceCodeStatus);

        self::assertSame('test business name', $result->legalUnit->businessName);
        self::assertSame(EntityType::Public, $result->legalUnit->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->legalUnit->administrativeStatus);
    }

    public function testThrowsIfNotFound(): void
    {
        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->method('getFacilityBySiret')
            ->willReturn(null);

        $action = new GetFacilityBySiret($repository);

        $this->expectException(ObjectNotFoundException::class);

        $action->__invoke('12345678900000');
    }
}
