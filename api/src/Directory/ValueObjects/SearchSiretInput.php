<?php

declare(strict_types=1);

namespace App\Directory\ValueObjects;

use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretSortingInner;

final readonly class SearchSiretInput
{
    /**
     * @param array<FacilityPayloadHistoryOutput> $results
     */
    /**
     * @param array<SearchSiretSortingInner> $sorting
     */
    public function __construct(
        public ?int $limit = 25,
        // TODO public int $ignore,
        // TODO $include
        public SearchSiretFilters $filters,
        public ?array $sorting = null,
        public ?array $fields = null,
        public ?array $results = null,
    ) {
    }
}
