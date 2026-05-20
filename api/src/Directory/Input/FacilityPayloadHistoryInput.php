<?php

declare(strict_types=1);

namespace App\Directory\Input;

use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @see \App\Directory\DTO\FacilityPayloadHistoryInfo
 */
class FacilityPayloadHistoryInput
{
    #[Assert\Positive]
    public ?int $idInstance = null;

    #[Assert\Length(max: 9)]
    public ?string $siren = null;

    #[Assert\Length(max: 14)]
    public ?string $siret = null;

    #[Assert\Length(max: 100)]
    public ?string $name = null;

    #[Assert\Choice(callback: [FacilityType::class, 'cases'])]
    public ?FacilityType $facilityType = null;

    #[Assert\Choice(callback: [DiffusionStatus::class, 'cases'])]
    public ?DiffusionStatus $diffusible = null;

    #[Assert\Choice(callback: [FacilityAdministrativeStatus::class, 'cases'])]
    public ?FacilityAdministrativeStatus $administrativeStatus = null;

    #[Assert\Length(max: 255)]
    public ?string $addressLines = null;

    #[Assert\Length(max: 20)]
    public ?string $postalCode = null;

    #[Assert\Length(max: 100)]
    public ?string $countrySubdivision = null;

    #[Assert\Length(max: 100)]
    public ?string $locality = null;
}
