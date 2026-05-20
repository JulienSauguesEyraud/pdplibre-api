<?php

declare(strict_types=1);

namespace App\Directory\Input;

use App\Directory\Enum\EntityType;
use App\Directory\Enum\StrictOperator;
use Symfony\Component\Validator\Constraints as Assert;

final class SearchSirenFiltersEntityType
{
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [EntityType::class, 'cases'])]

    public ?EntityType $entityType = null;
    public StrictOperator $operator = StrictOperator::opStrict;
}
