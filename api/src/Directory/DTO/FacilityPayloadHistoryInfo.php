<?php

declare(strict_types=1);

namespace App\Directory\DTO;

use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;

class FacilityPayloadHistoryInfo
{
    public function __construct(
        public ?int $idInstance = null,
        public ?string $siren = null,
        public ?string $siret = null,
        public ?string $name = null,
        public ?FacilityType $facilityType = null,
        public ?DiffusionStatus $diffusible = null,
        public ?FacilityAdministrativeStatus $administrativeStatus = null,
        public ?string $addressLines = null,
        public ?string $postalCode = null,
        public ?string $countrySubdivision = null,
        public ?string $locality = null,
    ) {
    }
}
