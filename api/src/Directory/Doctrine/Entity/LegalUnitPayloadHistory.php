<?php

declare(strict_types=1);

namespace App\Directory\Doctrine\Entity;

use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class LegalUnitPayloadHistory
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_instance', unique: true)]
    private int $idInstance;

    #[ORM\Column(name: 'siren' ,length: 9)]
    private string $siren;

    #[ORM\Column(name: 'business_name', length: 255)]
    private string $businessName;

    #[ORM\Column(name: 'entity_type')]
    private EntityType $entityType;

    #[ORM\Column(name: 'administrative_status')]
    private LegalUnitAdministrativeStatus $administrativeStatus;

    public static function create(int $idInstance, string $siren, string $businessName, EntityType $entityType, LegalUnitAdministrativeStatus $administrativeStatus,): self {
        $self = new self();

        $self->idInstance = $idInstance;
        $self->siren = $siren;
        $self->businessName = $businessName;
        $self->entityType = $entityType;
        $self->administrativeStatus = $administrativeStatus;

        return $self;
    }

    public function getIdInstance(): int
    {
        return $this->idInstance;
    }

    public function getSiren(): string
    {
        return $this->siren;
    }

    public function getBusinessName(): string
    {
        return $this->businessName;
    }

    public function getEntityType(): EntityType
    {
        return $this->entityType;
    }

    public function getAdministrativeStatus(): LegalUnitAdministrativeStatus
    {
        return $this->administrativeStatus;
    }
}
