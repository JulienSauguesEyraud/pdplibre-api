<?php

declare(strict_types=1);

namespace App\Flow\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Flow\ApiPlatform\StateProvider\HealthcheckCollectionProvider;
use App\Flow\ApiPlatform\StateProvider\HealthcheckProvider;

#[ApiResource(operations: [
    new GetCollection(
        provider: HealthcheckCollectionProvider::class,
    ),
    new Get(
        uriTemplate: '/v1/healthcheck',
        status: 200,
        openapi: new Operation(
            tags: ['Supervisor'],
            responses: [
                '200' => new Response('Service is healthy'),
                '500' => new Response('Internal server error'),
                '503' => new Response('Service unavailable — database unreachable'),
            ],
            summary: 'Check whether the API service is up and running.',
        ),
        output: false,
        name: 'getHealth',
        provider: HealthcheckProvider::class,
    ),
])]
final class HealthcheckResource
{
}
