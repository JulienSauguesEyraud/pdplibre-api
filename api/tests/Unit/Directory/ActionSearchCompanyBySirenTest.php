<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Directory\Actions\SearchCompanyBySiren as SearchCompanyBySirenAction;
use App\Directory\ApiPlatform\ApiResource\SearchCompanyBySiren;
use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use App\Directory\Enum\Order;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Input\SearchSirenFiltersBusinessName;
use App\Directory\Input\SearchSirenSortingInner;
use App\Directory\Repository\LegalUnitPayloadHistoryRepository;
use PHPUnit\Framework\TestCase;

final class ActionSearchCompanyBySirenTest extends TestCase
{
    public function testReturnsMappedResults(): void
    {
        $entity = $this->createMock(LegalUnitPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getBusinessName')->willReturn('test business name');
        $entity->method('getEntityType')->willReturn(EntityType::Public);
        $entity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $filters = new SearchSirenFilters();
        $filters->businessName = new SearchSirenFiltersBusinessName();
        $filters->businessName->businessName = 'test business name';

        $input = new SearchCompanyBySiren();
        $input->filters = $filters;

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('search')
            ->with($filters)
            ->willReturn([$entity]);

        $action = new SearchCompanyBySirenAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(25, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertNull($result->sorting);
        self::assertNull($result->fields);
        self::assertSame(1, $result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('test business name', $result->results[0]->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->administrativeStatus);
    }

    public function testReturnsMappedResultsWithSorting(): void
    {
        $entity = $this->createMock(LegalUnitPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getBusinessName')->willReturn('test business name');
        $entity->method('getEntityType')->willReturn(EntityType::Public);
        $entity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $filters = new SearchSirenFilters();
        $filters->businessName = new SearchSirenFiltersBusinessName();
        $filters->businessName->businessName = 'test business name';

        $sort1 = new SearchSirenSortingInner();
        $sort1->order = Order::ascending;
        $sort1->field = 'siren';

        $sort2 = new SearchSirenSortingInner();
        $sort2->order = Order::descending;
        $sort2->field = 'administrativeStatus';

        $sorting = [
            $sort1,
            $sort2
        ];

        $input = new SearchCompanyBySiren();
        $input->filters = $filters;
        $input->sorting = $sorting;

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('search')
            ->with(
                $filters,
                $sorting
            )
            ->willReturn([$entity]);

        $action = new SearchCompanyBySirenAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(25, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertSame($sorting, $result->sorting);
        self::assertNull($result->fields);
        self::assertSame(1, $result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('test business name', $result->results[0]->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->administrativeStatus);
    }

    public function testReturnsMappedResultsWithLimit(): void
    {
        $entity = $this->createMock(LegalUnitPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getBusinessName')->willReturn('test business name');
        $entity->method('getEntityType')->willReturn(EntityType::Public);
        $entity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $filters = new SearchSirenFilters();
        $filters->businessName = new SearchSirenFiltersBusinessName();
        $filters->businessName->businessName = 'test business name';

        $input = new SearchCompanyBySiren();
        $input->limit = 10;
        $input->filters = $filters;

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('search')
            ->with(
                $filters,
                null,
                10
            )
            ->willReturn([$entity]);

        $action = new SearchCompanyBySirenAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(10, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertNull($result->sorting);
        self::assertNull($result->fields);
        self::assertSame(1, $result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('test business name', $result->results[0]->businessName);
        self::assertSame(EntityType::Public, $result->results[0]->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->results[0]->administrativeStatus);
    }

    public function testReturnsMappedResultsWithFields(): void
    {
        $entity = $this->createMock(LegalUnitPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getBusinessName')->willReturn('test business name');
        $entity->method('getEntityType')->willReturn(EntityType::Public);
        $entity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $filters = new SearchSirenFilters();
        $filters->businessName = new SearchSirenFiltersBusinessName();
        $filters->businessName->businessName = 'test business name';

        $fields = [
            'siren',
            'businessName',
        ];

        $input = new SearchCompanyBySiren();
        $input->filters = $filters;
        $input->fields = $fields;

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('search')
            ->with($filters)
            ->willReturn([$entity]);

        $action = new SearchCompanyBySirenAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(25, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertNull($result->sorting);
        self::assertSame($fields, $result->fields);
        self::assertNull($result->results[0]->idInstance);
        self::assertSame('123456789', $result->results[0]->siren);
        self::assertSame('test business name', $result->results[0]->businessName);
        self::assertNull($result->results[0]->entityType);
        self::assertNull($result->results[0]->administrativeStatus);
    }

    public function testReturnsEmptyResults(): void
    {
        $filters = new SearchSirenFilters();
        $filters->businessName = new SearchSirenFiltersBusinessName();
        $filters->businessName->businessName = 'test business name';

        $sorting = [
            'businessName',
        ];

        $fields = [
            'siren',
            'businessName',
        ];

        $input = new SearchCompanyBySiren();
        $input->limit = 10;
        $input->filters = $filters;
        $input->sorting = $sorting;
        $input->fields = $fields;

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('search')
            ->with(
                $filters,
                $sorting,
                10
            )
            ->willReturn([]);

        $action = new SearchCompanyBySirenAction($repository);

        $result = $action->__invoke($input);

        self::assertSame(10, $result->limit);
        self::assertSame($filters, $result->filters);
        self::assertSame($sorting, $result->sorting);
        self::assertSame($fields, $result->fields);
        self::assertEmpty($result->results);
    }
}
