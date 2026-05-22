<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Directory\ApiPlatform\StateProcessor\SearchCompanyBySirenProcessor;
use App\Directory\ApiPlatform\StateProvider\GetFacilityByIdCollectionProvider;
use App\Directory\Enum\Order;
use App\Directory\Input\LegalUnitPayloadHistoryInput;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Input\SearchSirenSortingInner;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(operations: [
    new GetCollection(
        provider: GetFacilityByIdCollectionProvider::class,
    ),
    new Post(
        uriTemplate: '/v1/siren/search',
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
            summary: 'SIREN search (or legal unit)',
            description: 'Multi-criteria company search.',
        ),
        output: LegalUnitPayloadHistoryInput::class,
        name: 'searchCompanyBySiren',
        processor: SearchCompanyBySirenProcessor::class,
    ),
])]
final class SearchCompanyBySiren
{
    #[Assert\Range(min: 1, max: 100)]
    #[ApiProperty(
        description: 'Maximum number of results that may be returned',
        default: 25,
    )]
    public ?int $limit = 25;

    #[Assert\NotBlank]
    #[Assert\Valid]
    public SearchSirenFilters $filters;

    /**
     * @var array<SearchSirenSortingInner>|null
     */
    #[Assert\All(constraints: [
        new Assert\Type(SearchSirenSortingInner::class),
    ])]
    public ?array $sorting = null;

    #[Assert\Valid]
    public ?array $fields = null;

    // TODO rajouter ignore

    public function setSorting(?array $sorting): void
    {
        if (null === $sorting) {
            $this->sorting = null;

            return;
        }

        foreach ($sorting as $sort) {
            $sortingObj = new SearchSirenSortingInner();
            $sortingObj->field = $sort['field'] ?? null;
            $sortingObj->order = Order::from($sort['order']) ?? null;
            $this->sorting[] = $sortingObj;
        }
    }
}
