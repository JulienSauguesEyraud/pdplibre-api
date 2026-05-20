<?php

declare(strict_types=1);

namespace App\Directory\Input;

use App\Directory\Enum\LegalUnitAdministrativeStatus;
use App\Directory\Enum\StrictOperator;
use Symfony\Component\Validator\Constraints as Assert;

final class SearchSirenFiltersAdministrativeStatus
{
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [LegalUnitAdministrativeStatus::class, 'cases'])]
    public ?LegalUnitAdministrativeStatus $administrativeStatus = null;
    public StrictOperator $operator = StrictOperator::opStrict;
}
