<?php

declare(strict_types=1);

namespace App\Flow\ApiPlatform\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Flow\ApiPlatform\StateProcessor\CreateFlowProcessor;
use App\Flow\DTO\FullFlowInfo;
use App\Flow\Input\FlowInfoInput;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(operations: [
    new GetCollection(),
    new Post(
        uriTemplate: '/v1/flows',
        inputFormats: ['multipart'],
        outputFormats: ['json'],
        status: 202,
        openapi: new Operation(
            responses: [
                '202' => new Response(description: 'Accepted — flow submitted for processing'),
                '400' => new Response(description: 'Bad request — invalid input format'),
                '401' => new Response(description: 'Unauthorized — authentication required'),
                '403' => new Response(description: 'Forbidden — insufficient permissions'),
                '413' => new Response(description: 'Payload too large — file exceeds size limit'),
                '422' => new Response(description: 'Unprocessable entity — validation error'),
                '429' => new Response(description: 'Too many requests — rate limit exceeded'),
                '500' => new Response(description: 'Internal server error'),
                '503' => new Response(description: 'Service unavailable'),
            ],
            summary: 'Submit a new flow',
            description: 'Submits a new flow for processing. The flow file and its metadata are validated and stored. Processing is performed asynchronously.',
        ),
        denormalizationContext: [
            'collect_denormalization_errors' => true,
        ],
        output: FullFlowInfo::class,
        validate: false,
        name: 'createFlow',
        processor: CreateFlowProcessor::class,
    ),
])]
final class CreateFlowResource
{
    #[Assert\Valid()]
    #[Assert\NotNull]
    public ?FlowInfoInput $flowInfo = null;

    #[Assert\File(
        mimeTypes: ['application/pdf', 'application/xml'],
    )]
    #[Assert\NotNull]
    public ?UploadedFile $file = null;
}
