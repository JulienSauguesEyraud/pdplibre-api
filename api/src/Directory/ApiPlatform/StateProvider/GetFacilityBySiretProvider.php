<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\StateProvider;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Common\ApiPlatform\ApiValidationException;
use App\Common\Exception\InvalidInputException;
use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Actions\GetFacilityBySiret as GetFacilityBySiretAction;
use App\Directory\ApiPlatform\ApiResource\GetFacilityBySiret;
use App\Directory\Validation\GetFacilityBySiretValidator;
use App\User\Doctrine\Entity\ApiConsumer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetFacilityBySiretProvider implements ProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private GetFacilityBySiretAction $action,
        private GetFacilityBySiretValidator $validator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        assert($operation instanceof Get);

        $currentUser = $this->tokenStorage->getToken()?->getUser();
        assert($currentUser instanceof ApiConsumer);
        assert('getFacilityBySiret' === $operation->getName());
        assert(GetFacilityBySiret::class === $operation->getClass());

        assert(isset($uriVariables['siret']));

        /** @var Request|null $request */
        $request = $context['request'] ?? null;

        $fields = $request?->query->all('fields');

        try {
            $this->validator->validate($uriVariables['siret'], $fields);
            $result = $this->action->__invoke($uriVariables['siret'], $fields);
        } catch (ObjectNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (InvalidInputException $e) {
            throw ApiValidationException::create($e->path, $e->getMessage(), $e);
        }

        return $result;
    }
}
