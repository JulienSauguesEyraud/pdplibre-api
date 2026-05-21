<?php

declare(strict_types=1);

namespace App\Flow\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Flow\ApiPlatform\StateProcessor\SearchFlowProcessor;
use App\Flow\ApiPlatform\StateProvider\CreateFlowCollectionProvider;
use App\Flow\ApiPlatform\StateProvider\FlowSearchRequestCollectionProvider;
use App\Flow\Input\SearchFlowFilters;
use App\Flow\ValueObjects\SearchFlowInput;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(operations: [
    new GetCollection(
        provider: FlowSearchRequestCollectionProvider::class,
    ),
    new Post(
        uriTemplate: '/v1/flows/search',
        outputFormats: ['json'],
        status: 200,
        openapi: new Operation(
            responses: [
                '200' => new Response(description: 'Successful operation'),
                '400' => new Response(description: 'Bad request — invalid input format'),
                '401' => new Response(description: 'Unauthorized — authentication required'),
                '403' => new Response(description: 'Forbidden — insufficient permissions'),
                '422' => new Response(description: 'Unprocessable entity — validation error'),
                '500' => new Response(description: 'Internal server error'),
                '503' => new Response(description: 'Service unavailable'),
            ],
            summary: 'Select flows upon criteria',
            description: 'Retrieves a set of flows matching the provided search criteria. Requires at least one criterion. Combines criteria with logical AND; criteria allowing a list of values use logical OR. Pagination works with the updatedAfter property.',
        ),
        output: SearchFlowInput::class,
        name: 'searchFlow',
        processor: SearchFlowProcessor::class,
    ),
])]
final class FlowSearchRequestResource
{
    #[Assert\Range(min: 1, max: 100)]
    #[ApiProperty(
        description: 'Maximum number of results that may be returned',
        default: 25,
    )]
    public int $limit = 25;

    #[Assert\NotBlank]
    #[Assert\Valid]
    public ?SearchFlowFilters $where;
}
