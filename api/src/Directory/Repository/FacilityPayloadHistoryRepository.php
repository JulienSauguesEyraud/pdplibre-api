<?php

declare(strict_types=1);

namespace App\Directory\Repository;

use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretSortingInner;

interface FacilityPayloadHistoryRepository
{
    /**
     * @param array<string, mixed>             $criteria
     * @param array<string, "desc"|"asc">|null $orderBy
     *
     * @return array<FacilityPayloadHistory>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param array<string, mixed>             $criteria
     * @param array<string, "desc"|"asc">|null $orderBy
     *
     * @return FacilityPayloadHistory|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object;

    public function getFacilityById(int $id): ?FacilityPayloadHistory;

    public function getFacilityBySiret(string $siret): ?FacilityPayloadHistory;

    // TODO rajouter ignore
    // TODO rajouter include
    /**
     * @return array<FacilityPayloadHistory>
     */
    /**
     * @param array<SearchSiretSortingInner> $sorting
     */
    public function searchFacilityBySiret(SearchSiretFilters $filters, ?array $sorting, ?int $limit): array;

    public function getMaxVersion(int $idInstance): int;
}
