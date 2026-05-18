<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Actions\GetCompanyById;
use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use App\Directory\Repository\LegalUnitPayloadHistoryRepository;
use PHPUnit\Framework\TestCase;

final class ActionGetCompanyById extends TestCase
{
    public function testReturnsOutput(): void
    {
        $entity = $this->createMock(LegalUnitPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getBusinessName')->willReturn('test business name');
        $entity->method('getEntityType')->willReturn(EntityType::Public);
        $entity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('getSirenByIdInstance')
            ->with(1)
            ->willReturn($entity);

        $action = new GetCompanyById($repository);

        $result = $action->__invoke(1);

        self::assertSame(1, $result->idInstance);
        self::assertSame('123456789', $result->siren);
        self::assertSame('test business name', $result->businessName);
        self::assertSame(EntityType::Public, $result->entityType);
        self::assertSame(LegalUnitAdministrativeStatus::A, $result->administrativeStatus);
    }

    public function testReturnsOutputWithFields(): void
    {
        $entity = $this->createMock(LegalUnitPayloadHistory::class);

        $entity->method('getIdInstance')->willReturn(1);
        $entity->method('getSiren')->willReturn('123456789');
        $entity->method('getBusinessName')->willReturn('test business name');
        $entity->method('getEntityType')->willReturn(EntityType::Public);
        $entity->method('getAdministrativeStatus')->willReturn(LegalUnitAdministrativeStatus::A);

        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->expects($this->once())
            ->method('getSirenByIdInstance')
            ->with(1)
            ->willReturn($entity);

        $action = new GetCompanyById($repository);

        $result = $action->__invoke(1, ['siren', 'businessName']);

        self::assertNull($result->idInstance);
        self::assertSame('123456789', $result->siren);
        self::assertSame('test business name', $result->businessName);
        self::assertNull($result->entityType);
        self::assertNull($result->administrativeStatus);
    }

    public function testThrowsIfNotFound(): void
    {
        $repository = $this->createMock(LegalUnitPayloadHistoryRepository::class);

        $repository
            ->method('getSirenByIdInstance')
            ->willReturn(null);

        $action = new GetCompanyById($repository);

        $this->expectException(ObjectNotFoundException::class);

        $action->__invoke(1);
    }
}
