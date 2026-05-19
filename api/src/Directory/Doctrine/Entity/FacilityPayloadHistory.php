<?php

declare(strict_types=1);

namespace App\Directory\Doctrine\Entity;

use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_instance_version', columns: ['id_instance', 'version'])]
class FacilityPayloadHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', unique: true)]
    private int $id;

    #[ORM\Column(name: 'id_instance')]
    private int $idInstance;

    #[ORM\Column(name: 'siret', length: 14)]
    private string $siret;

    #[ORM\Column(name: 'siren', length: 9)]
    private string $siren;

    #[ORM\Column(name: 'name', length: 100)]
    private string $name;

    #[ORM\Column(name: 'facility_type')]
    private FacilityType $facilityType;

    #[ORM\Column(name: 'diffusible')]
    private DiffusionStatus $diffusible;

    #[ORM\Column(name: 'administrative_status')]
    private FacilityAdministrativeStatus $administrativeStatus;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id', nullable: false)]
    private AddressRead $address;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'b2g_additional_data_id', referencedColumnName: 'id', nullable: false)]
    private B2gAdditionalData $b2gAdditionalData;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'legal_unit_id', referencedColumnName: 'id', nullable: false)]
    private LegalUnitPayloadHistory $legalUnit;

    #[ORM\Column(name: 'version')]
    private int $version;

    #[ORM\Column(name: 'updated_at')]
    private \DateTimeImmutable $updatedAt;

    public static function create(int $idInstance, string $siret, string $siren, string $name, FacilityType $facilityType, DiffusionStatus $diffusible, FacilityAdministrativeStatus $administrativeStatus, AddressRead $address, B2gAdditionalData $b2gAdditionalData, LegalUnitPayloadHistory $legalUnit, int $version = 1): FacilityPayloadHistory
    {
        $self = new self();
        $self->idInstance = $idInstance;
        $self->siren = $siren;
        $self->siret = $siret;
        $self->name = $name;
        $self->facilityType = $facilityType;
        $self->diffusible = $diffusible;
        $self->administrativeStatus = $administrativeStatus;
        $self->address = $address;
        $self->b2gAdditionalData = $b2gAdditionalData;
        $self->legalUnit = $legalUnit;

        $self->updatedAt = new \DateTimeImmutable();
        $self->version = $version;

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

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFacilityType(): FacilityType
    {
        return $this->facilityType;
    }

    public function getDiffusible(): DiffusionStatus
    {
        return $this->diffusible;
    }

    public function getAdministrativeStatus(): FacilityAdministrativeStatus
    {
        return $this->administrativeStatus;
    }

    public function getAddress(): AddressRead
    {
        return $this->address;
    }

    public function getB2gAdditionalData(): B2gAdditionalData
    {
        return $this->b2gAdditionalData;
    }

    public function getLegalUnit(): LegalUnitPayloadHistory
    {
        return $this->legalUnit;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
