<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Directory\ApiPlatform\StateProvider\GetFacilityByIdProvider;
use App\Directory\ValueObjects\FacilityPayloadHistoryOutput;

#[ApiResource(operations: [
    new GetCollection(),
    new Get(
        uriTemplate: '/v1/siret/id-instance:{idInstance}',
        outputFormats: ['json'],
        output: FacilityPayloadHistoryOutput::class,
        validate: false,
        name: 'getFacilityById',
        provider: GetFacilityByIdProvider::class,
        parameters: [
            'fields' => new QueryParameter(),
        ],
    ),
])]
final class GetFacilityById
{
}
