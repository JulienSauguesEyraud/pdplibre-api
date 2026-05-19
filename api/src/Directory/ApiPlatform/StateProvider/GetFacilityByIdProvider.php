<?php

declare(strict_types=1);

namespace App\Directory\ApiPlatform\StateProvider;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Common\ApiPlatform\ApiValidationException;
use App\Common\Exception\InvalidInputException;
use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Actions\GetFacilityById as GetFacilityByIdAction;
use App\Directory\ApiPlatform\ApiResource\GetFacilityById;
use App\Directory\Validation\GetFacilityByIdValidator;
use App\User\Doctrine\Entity\ApiConsumer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetFacilityByIdProvider implements ProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private GetFacilityByIdAction $action,
        private GetFacilityByIdValidator $validator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        assert($operation instanceof Get);

        $currentUser = $this->tokenStorage->getToken()?->getUser();
        assert($currentUser instanceof ApiConsumer);
        assert('getSiretByIdInstance' === $operation->getName());
        assert(GetFacilityById::class === $operation->getClass());

        assert(isset($uriVariables['idInstance']));

        /** @var Request|null $request */
        $request = $context['request'] ?? null;

        $fields = $request?->query->all('fields');

        try {
            $this->validator->validate((int) $uriVariables['idInstance'], $fields);
            $result = $this->action->__invoke((int) $uriVariables['idInstance'], $fields);
        } catch (ObjectNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (InvalidInputException $e) {
            throw ApiValidationException::create($e->path, $e->getMessage(), $e);
        }

        return $result;
    }
}
