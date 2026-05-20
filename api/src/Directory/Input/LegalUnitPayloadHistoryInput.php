<?php

declare(strict_types=1);

namespace App\Directory\Input;

use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @see \App\Directory\DTO\LegalUnitPayloadHistoryInfo
 */
class LegalUnitPayloadHistoryInput
{
    #[Assert\Positive]
    public ?int $idInstance = null;

    #[Assert\Length(max: 9)]
    public ?string $siren = null;

    #[Assert\Length(max: 150)]
    public ?string $businessName = null;

    #[Assert\Choice(callback: [EntityType::class, 'cases'])]
    public ?EntityType $entityType = null;

    #[Assert\Choice(callback: [LegalUnitAdministrativeStatus::class, 'cases'])]
    public ?LegalUnitAdministrativeStatus $legalUnitAdministrativeStatus = null;
}
