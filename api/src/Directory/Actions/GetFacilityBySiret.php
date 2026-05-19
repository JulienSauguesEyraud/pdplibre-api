<?php

declare(strict_types=1);

namespace App\Directory\Actions;

use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Repository\FacilityPayloadHistoryRepository;
use App\Directory\ValueObjects\FacilityPayloadHistoryOutput;

final readonly class GetFacilityBySiret
{
    public function __construct(
        private FacilityPayloadHistoryRepository $repository,
    ) {
    }

    public function __invoke(string $siret, ?array $fields = null): FacilityPayloadHistoryOutput
    {
        $facilityPayloadHistory = $this->repository->getFacilityBySiret($siret);

        if (!$facilityPayloadHistory) {
            throw new ObjectNotFoundException('SIREN not found');
        }

        return FacilityPayloadHistoryOutput::fromEntity($facilityPayloadHistory, $fields);
    }
}
