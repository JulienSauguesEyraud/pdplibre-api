<?php

declare(strict_types=1);

namespace App\Directory\Actions;

use App\Common\Exception\ObjectNotFoundException;
use App\Directory\Repository\LegalUnitPayloadHistoryRepository;
use App\Directory\ValueObjects\LegalUnitPayloadHistoryOutput;

final readonly class GetCompanyBySiren
{
    public function __construct(
        private LegalUnitPayloadHistoryRepository $repository,
    ) {
    }

    public function __invoke(string $siren, ?array $fields = null): LegalUnitPayloadHistoryOutput
    {
        $legalUnitPayloadHistory = $this->repository->getCompanyBySiren($siren);

        if (!$legalUnitPayloadHistory) {
            throw new ObjectNotFoundException('SIREN not found');
        }

        return LegalUnitPayloadHistoryOutput::fromEntity($legalUnitPayloadHistory, $fields);
    }
}
