<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Directory\ApiPlatform\StateProcessor\SearchSiretProcessor;
use App\Directory\Input\FacilityPayloadHistoryInput;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretSorting;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(operations: [
    new Post(
        uriTemplate: '/v1/siret/search',
        outputFormats: ['json'],
        status: 200,
        openapi: new Operation(
            responses: [
                '200' => new Response(description: 'Returns one or more companies.'),
                '206' => new Response(description: 'Request processed without error, but the volume of information returned has been reduced.'),
                '400' => new Response(description: 'Bad request. The request is invalid or cannot be completed.'),
                '401' => new Response(description: 'Unauthorized. The request requires user authentication.'),
                '403' => new Response(description: 'Forbidden. The server understood the request but denied access or access is not authorized.'),
                '404' => new Response(description: 'Not found. There is no resource at the given URI.'),
                '408' => new Response(description: 'Request timeout exceeded.'),
                '422' => new Response(description: 'Data validation error.'),
                '429' => new Response(description: 'The client has issued too many calls within a given time frame.'),
                '500' => new Response(description: 'Internal Server Error.'),
                '501' => new Response(description: 'Not implemented.'),
                '503' => new Response(description: 'Service unavailable'),
            ],
            summary: 'Search for a SIRET (facility)',
            description: 'Multi-criteria search for facilities.',
        ),
        output: FacilityPayloadHistoryInput::class,
        name: 'searchSiret',
        processor: SearchSiretProcessor::class,
    ),
])]

final class SiretSearchRequestResource
{
    #[Assert\Range(min: 1, max: 100)]
    #[ApiProperty(
        description: 'Maximum number of results that may be returned',
        default: 25,
    )]
    public ?int $limit = 25;

    #[Assert\NotBlank]
    #[Assert\Valid]
    public SearchSiretFilters $filters;

    /**
     * @var array<SearchSiretSorting>|null
     */
    #[Assert\All(constraints: [
        new Assert\Type(SearchSiretSorting::class),
    ])]
    public ?array $sorting = null;

    public ?array $fields = null;

    // TODO rajouter ignore
    // TODO rajouter include
}
