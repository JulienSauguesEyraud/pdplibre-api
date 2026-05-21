<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Directory\ApiPlatform\StateProvider\GetCompanyByIdCollectionProvider;
use App\Directory\ValueObjects\LegalUnitPayloadHistoryOutput;

#[ApiResource(operations: [
    new GetCollection(
        provider: GetCompanyByIdCollectionProvider::class,
    ),
    new Get(
        uriTemplate: '/v1/siren/id-instance:{idInstance}',
        outputFormats: ['json'],
        output: LegalUnitPayloadHistoryOutput::class,
        validate: false,
        name: 'getCompanyById',
        provider: GetCompanyByIdCollectionProvider::class,
        parameters: [
            'fields' => new QueryParameter(),
        ],
    ),
])]
final class GetCompanyById
{
}
