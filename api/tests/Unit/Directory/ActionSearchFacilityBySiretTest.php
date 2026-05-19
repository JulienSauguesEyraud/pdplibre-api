<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Directory\Actions\SearchFacilityBySiret as SearchFacilityBySiretAction;
use App\Directory\ApiPlatform\ApiResource\SearchFacilityBySiret;
use App\Directory\Doctrine\Entity\AddressRead;
use App\Directory\Doctrine\Entity\B2gAdditionalData;
use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use App\Directory\Enum\Order;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretFiltersName;
use App\Directory\Input\SearchSiretSorting;
use App\Directory\Repository\FacilityPayloadHistoryRepository;
use PHPUnit\Framework\TestCase;

final class ActionSearchFacilityBySiretTest extends TestCase
{
    public function testReturnsMappedResults(): void
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

        $filters = new SearchSiretFilters();
        $filters->name = new SearchSiretFiltersName();
        $filters->name->name = 'test name';

        $input = new SearchFacilityBySiret();
        $input->filters = $filters;

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('searchFacilityBySiret')
            ->with($filters)
            ->willReturn([$entity]);

        $action = new SearchFacilityBySiretAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(25, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertNull($result->sorting);
        self::assertNull($result->fields);
        self::assertSame(1, $result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('12345678900000', $result->results[0]->siret);
        self::assertSame('test name', $result->results[0]->name);
        self::assertSame(FacilityType::P, $result->results[0]->facilityType);
        self::assertSame(DiffusionStatus::P, $result->results[0]->diffusible);
        self::assertSame(FacilityAdministrativeStatus::A, $result->results[0]->administrativeStatus);

        self::assertSame('address 1', $result->results[0]->address->addressLine1);
        self::assertSame('address 2', $result->results[0]->address->addressLine2);
        self::assertSame('address 3', $result->results[0]->address->addressLine3);
        self::assertSame('12345', $result->results[0]->address->postalCode);
        self::assertSame('subdivision', $result->results[0]->address->countrySubdivision);
        self::assertSame('locality', $result->results[0]->address->locality);
        self::assertSame('FR', $result->results[0]->address->countryCode);
        self::assertSame('France', $result->results[0]->address->countryName);

        self::assertSame(true, $result->results[0]->b2gAdditionalData->pm);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->pmOnly);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesPaymentStatus);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesLegalCommitmentCode);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesLegalCommitmentOrServiceCode);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->serviceCodeStatus);

        self::assertSame('test business name', $result->results[0]->legalUnit->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->legalUnit->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->legalUnit->administrativeStatus);
    }

    public function testReturnsMappedResultsWithSorting(): void
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

        $filters = new SearchSiretFilters();
        $filters->name = new SearchSiretFiltersName();
        $filters->name->name = 'test name';

        $sort1 = new SearchSiretSorting();
        $sort1->order = Order::ascending;
        $sort1->field = 'siren';

        $sort2 = new SearchSiretSorting();
        $sort2->order = Order::descending;
        $sort2->field = 'administrativeStatus';

        $sorting = [
            $sort1,
            $sort2,
        ];

        $input = new SearchFacilityBySiret();
        $input->filters = $filters;
        $input->sorting = $sorting;

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('searchFacilityBySiret')
            ->with(
                $filters,
                $sorting
            )
            ->willReturn([$entity]);

        $action = new SearchFacilityBySiretAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(25, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertSame($sorting, $result->sorting);
        self::assertNull($result->fields);
        self::assertSame(1, $result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('12345678900000', $result->results[0]->siret);
        self::assertSame('test name', $result->results[0]->name);
        self::assertSame(FacilityType::P, $result->results[0]->facilityType);
        self::assertSame(DiffusionStatus::P, $result->results[0]->diffusible);
        self::assertSame(FacilityAdministrativeStatus::A, $result->results[0]->administrativeStatus);

        self::assertSame('address 1', $result->results[0]->address->addressLine1);
        self::assertSame('address 2', $result->results[0]->address->addressLine2);
        self::assertSame('address 3', $result->results[0]->address->addressLine3);
        self::assertSame('12345', $result->results[0]->address->postalCode);
        self::assertSame('subdivision', $result->results[0]->address->countrySubdivision);
        self::assertSame('locality', $result->results[0]->address->locality);
        self::assertSame('FR', $result->results[0]->address->countryCode);
        self::assertSame('France', $result->results[0]->address->countryName);

        self::assertSame(true, $result->results[0]->b2gAdditionalData->pm);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->pmOnly);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesPaymentStatus);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesLegalCommitmentCode);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesLegalCommitmentOrServiceCode);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->serviceCodeStatus);

        self::assertSame('test business name', $result->results[0]->legalUnit->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->legalUnit->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->legalUnit->administrativeStatus);
    }

    public function testReturnsMappedResultsWithLimit(): void
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

        $filters = new SearchSiretFilters();
        $filters->name = new SearchSiretFiltersName();
        $filters->name->name = 'test name';

        $input = new SearchFacilityBySiret();
        $input->filters = $filters;
        $input->limit = 10;

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('searchFacilityBySiret')
            ->with(
                $filters,
                null,
                10
            )
            ->willReturn([$entity]);

        $action = new SearchFacilityBySiretAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(10, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertNull($result->sorting);
        self::assertNull($result->fields);
        self::assertSame(1, $result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('12345678900000', $result->results[0]->siret);
        self::assertSame('test name', $result->results[0]->name);
        self::assertSame(FacilityType::P, $result->results[0]->facilityType);
        self::assertSame(DiffusionStatus::P, $result->results[0]->diffusible);
        self::assertSame(FacilityAdministrativeStatus::A, $result->results[0]->administrativeStatus);

        self::assertSame('address 1', $result->results[0]->address->addressLine1);
        self::assertSame('address 2', $result->results[0]->address->addressLine2);
        self::assertSame('address 3', $result->results[0]->address->addressLine3);
        self::assertSame('12345', $result->results[0]->address->postalCode);
        self::assertSame('subdivision', $result->results[0]->address->countrySubdivision);
        self::assertSame('locality', $result->results[0]->address->locality);
        self::assertSame('FR', $result->results[0]->address->countryCode);
        self::assertSame('France', $result->results[0]->address->countryName);

        self::assertSame(true, $result->results[0]->b2gAdditionalData->pm);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->pmOnly);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesPaymentStatus);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesLegalCommitmentCode);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->managesLegalCommitmentOrServiceCode);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->serviceCodeStatus);

        self::assertSame('test business name', $result->results[0]->legalUnit->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->legalUnit->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->legalUnit->administrativeStatus);
    }

    public function testReturnsMappedResultsWithFields(): void
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

        $filters = new SearchSiretFilters();
        $filters->name = new SearchSiretFiltersName();
        $filters->name->name = 'test name';

        $fields = [
            'siret',
            'name',
            'facilityType',
            'address',
            'pmOnly',
        ];

        $input = new SearchFacilityBySiret();
        $input->filters = $filters;
        $input->fields = $fields;

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('searchFacilityBySiret')
            ->with($filters)
            ->willReturn([$entity]);

        $action = new SearchFacilityBySiretAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(25, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertNull($result->sorting);
        self::assertSame($fields, $result->fields);
        self::assertNull($result->results[0]->idInstance);
        self::assertNull($result->results[0]->siren);
        self::assertSame('12345678900000', $result->results[0]->siret);
        self::assertSame('test name', $result->results[0]->name);
        self::assertSame(FacilityType::P, $result->results[0]->facilityType);
        self::assertNull($result->results[0]->diffusible);
        self::assertNull($result->results[0]->administrativeStatus);

        self::assertSame('address 1', $result->results[0]->address->addressLine1);
        self::assertSame('address 2', $result->results[0]->address->addressLine2);
        self::assertSame('address 3', $result->results[0]->address->addressLine3);
        self::assertSame('12345', $result->results[0]->address->postalCode);
        self::assertSame('subdivision', $result->results[0]->address->countrySubdivision);
        self::assertSame('locality', $result->results[0]->address->locality);
        self::assertSame('FR', $result->results[0]->address->countryCode);
        self::assertSame('France', $result->results[0]->address->countryName);

        self::assertNull($result->results[0]->b2gAdditionalData->pm);
        self::assertSame(true, $result->results[0]->b2gAdditionalData->pmOnly);
        self::assertNull($result->results[0]->b2gAdditionalData->managesPaymentStatus);
        self::assertNull($result->results[0]->b2gAdditionalData->managesLegalCommitmentCode);
        self::assertNull($result->results[0]->b2gAdditionalData->managesLegalCommitmentOrServiceCode);
        self::assertNull($result->results[0]->b2gAdditionalData->serviceCodeStatus);

        self::assertSame('test business name', $result->results[0]->legalUnit->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->legalUnit->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->legalUnit->administrativeStatus);
    }

    public function testReturnsEmptyResults(): void
    {
        $filters = new SearchSiretFilters();
        $filters->name = new SearchSiretFiltersName();
        $filters->name->name = 'test name';

        $sorting = [
            'name',
        ];

        $fields = [
            'siren',
            'name',
            'administrativeStatus',
        ];

        $input = new SearchFacilityBySiret();
        $input->limit = 10;
        $input->filters = $filters;
        $input->sorting = $sorting;
        $input->fields = $fields;

        $repository = $this->createMock(FacilityPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('searchFacilityBySiret')
            ->with(
                $filters,
                $sorting,
                10
            )
            ->willReturn([]);

        $action = new SearchFacilityBySiretAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(10, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertSame($sorting, $result->sorting);
        self::assertSame($fields, $result->fields);
        self::assertEmpty($result->results);
    }
}
