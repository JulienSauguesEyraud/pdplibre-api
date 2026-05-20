<?php

declare(strict_types=1);

namespace App\Directory\Input;

use App\Directory\Enum\ContainsOperator;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\FacilityType;
use Symfony\Component\Validator\Constraints as Assert;

final class SearchSiretFiltersFacilityType
{
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [FacilityType::class, 'cases'])]

    public ?EntityType $entityType = null;
    public ContainsOperator $operator = ContainsOperator::opContains;
}
