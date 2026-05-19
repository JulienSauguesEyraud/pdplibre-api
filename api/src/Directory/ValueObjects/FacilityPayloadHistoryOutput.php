<?php

declare(strict_types=1);

namespace App\Directory\ValueObjects;

use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;

final class FacilityPayloadHistoryOutput
{
    public ?int $idInstance = null;
    public ?string $siret = null;
    public ?string $siren = null;
    public ?string $name = null;
    public ?FacilityType $facilityType = null;
    public ?DiffusionStatus $diffusible = null;
    public ?FacilityAdministrativeStatus $administrativeStatus = null;
    public ?AddressReadOutput $address = null;
    public ?B2gAdditionalDataOutput $b2gAdditionalData = null;
    public ?LegalUnitPayloadIncludedNoSirenOutput $legalUnit = null;

    public static function fromEntity(FacilityPayloadHistory $facilityPayloadHistory, ?array $fields = null): self
    {
        $self = new self();

        $returnAll = !$fields;

        if ($returnAll || in_array('idInstance', $fields, true)) {
            $self->idInstance = $facilityPayloadHistory->getIdInstance();
        }

        if ($returnAll || in_array('siret', $fields, true)) {
            $self->siret = $facilityPayloadHistory->getSiret();
        }

        if ($returnAll || in_array('siren', $fields, true)) {
            $self->siren = $facilityPayloadHistory->getSiren();
        }

        if ($returnAll || in_array('name', $fields, true)) {
            $self->name = $facilityPayloadHistory->getName();
        }

        if ($returnAll || in_array('facilityType', $fields, true)) {
            $self->facilityType = $facilityPayloadHistory->getFacilityType();
        }

        if ($returnAll || in_array('diffusible', $fields, true)) {
            $self->diffusible = $facilityPayloadHistory->getDiffusible();
        }

        if ($returnAll || in_array('administrativeStatus', $fields, true)) {
            $self->administrativeStatus = $facilityPayloadHistory->getAdministrativeStatus();
        }

        if ($returnAll || in_array('address', $fields, true)) {
            $self->address = AddressReadOutput::fromEntity($facilityPayloadHistory->getAddress());
        }

        if ($returnAll) {
            $self->b2gAdditionalData = B2gAdditionalDataOutput::fromEntity($facilityPayloadHistory->getB2gAdditionalData());
        } else {
            $b2gAdditionalDataFields = [];

            if (in_array('pmStatus', $fields, true)) {
                $b2gAdditionalDataFields[] = 'pmStatus';
            }

            if (in_array('pmOnly', $fields, true)) {
                $b2gAdditionalDataFields[] = 'pmOnly';
            }

            if (in_array('managesPaymentStatus', $fields, true)) {
                $b2gAdditionalDataFields[] = 'managesPaymentStatus';
            }

            if (in_array('managesLegalCommitment', $fields, true)) {
                $b2gAdditionalDataFields[] = 'managesLegalCommitment';
            }

            if (in_array('managesLegalCommitmentOrService', $fields, true)) {
                $b2gAdditionalDataFields[] = 'managesLegalCommitmentOrService';
            }

            if (in_array('serviceCodeStatus', $fields, true)) {
                $b2gAdditionalDataFields[] = 'serviceCodeStatus';
            }

            if ($b2gAdditionalDataFields) {
                $self->b2gAdditionalData = B2gAdditionalDataOutput::fromEntity($facilityPayloadHistory->getB2gAdditionalData(), $b2gAdditionalDataFields);
            }
        }

        $self->legalUnit = LegalUnitPayloadIncludedNoSirenOutput::fromEntity($facilityPayloadHistory->getLegalUnit());

        return $self;
    }
}
