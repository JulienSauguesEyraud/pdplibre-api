<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\QueryParameter;
use App\Directory\ApiPlatform\StateProvider\GetFacilityBySiretProvider;
use App\Directory\ValueObjects\FacilityPayloadHistoryOutput;

#[ApiResource(operations: [
    new Get(
        uriTemplate: '/v1/siret/code-insee:{siret}',
        outputFormats: ['json'],
        output: FacilityPayloadHistoryOutput::class,
        validate: false,
        name: 'getFacilityBySiret',
        provider: GetFacilityBySiretProvider::class,
        parameters: [
            'fields' => new QueryParameter(),
        ],
    ),
])]
final class GetFacilityBySiret
{
}
