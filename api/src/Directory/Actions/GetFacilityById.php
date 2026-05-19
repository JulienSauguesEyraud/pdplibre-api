<?php

declare(strict_types=1);

namespace App\Directory\Actions;

use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Repository\FacilityPayloadHistoryRepository;
use App\Directory\ValueObjects\FacilityPayloadHistoryOutput;

final readonly class GetFacilityById
{
    public function __construct(
        private FacilityPayloadHistoryRepository $repository,
    ) {
    }

    public function __invoke(int $idInstance, ?array $fields = null): FacilityPayloadHistoryOutput
    {
        $facilityPayloadHistory = $this->repository->getSiretByIdInstance($idInstance);

        if (!$facilityPayloadHistory) {
            throw new ObjectNotFoundException('SIREN not found');
        }

        return FacilityPayloadHistoryOutput::fromEntity($facilityPayloadHistory, $fields);
    }
}
