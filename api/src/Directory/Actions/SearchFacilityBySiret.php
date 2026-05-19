<?php

declare(strict_types=1);

namespace App\Directory\Actions;

use App\Directory\ApiPlatform\ApiResource\SearchFacilityBySiret as SearchFacilityBySiretResource;
use App\Directory\Repository\FacilityPayloadHistoryRepository;
use App\Directory\ValueObjects\FacilityPayloadHistoryOutput;
use App\Directory\ValueObjects\SearchSiretInput;

final readonly class SearchFacilityBySiret
{
    public function __construct(
        private FacilityPayloadHistoryRepository $repository,
    ) {
    }

    public function __invoke(SearchFacilityBySiretResource $input): SearchSiretInput
    {
        $entities = $this->repository->searchFacilityBySiret(
            $input->filters,
            $input->sorting,
            $input->limit
        );

        $results = [];

        foreach ($entities as $entity) {
            $results[] = FacilityPayloadHistoryOutput::fromEntity($entity, $input->fields);
        }

        return new SearchSiretInput(
            limit: $input->limit,
            filters: $input->filters,
            sorting: $input->sorting,
            fields: $input->fields,
            results: $results
        );
    }
}
