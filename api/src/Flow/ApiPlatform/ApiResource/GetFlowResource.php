<?php

declare(strict_types=1);

namespace App\Flow\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Flow\ApiPlatform\StateProvider\GetFlowProvider;
use App\Flow\ValueObjects\FlowOutput;

#[ApiResource(operations: [
    new GetCollection(),
    new Get(
        uriTemplate: '/v1/flows/{flowId}',
        outputFormats: ['json'],
        output: FlowOutput::class,
        validate: false,
        name: 'getFlow',
        provider: GetFlowProvider::class,
        parameters: [
            'docType' => new QueryParameter(),
        ],
    ),
])]
final class GetFlowResource
{
}
