<?php

declare(strict_types=1);

namespace App\Directory\Doctrine\Repository;

use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\ContainsOperator;
use App\Directory\Enum\Order;
use App\Directory\Enum\StrictOperator;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Repository\LegalUnitPayloadHistoryRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LegalUnitPayloadHistory>
 */
final class DoctrineLegalUnitPayloadHistoryRepository extends ServiceEntityRepository implements LegalUnitPayloadHistoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalUnitPayloadHistory::class);
    }

    public function getCompanyById(int $id): ?LegalUnitPayloadHistory
    {
        return $this->createQueryBuilder('legalUnitPayloadHistory')
            ->where('legalUnitPayloadHistory.idInstance = :id')
            ->setParameter('id', $id)
            ->orderBy('legalUnitPayloadHistory.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCompanyBySiren(string $siren): ?LegalUnitPayloadHistory
    {
        return $this->createQueryBuilder('legalUnitPayloadHistory')
            ->where('legalUnitPayloadHistory.siren = :siren')
            ->setParameter('siren', $siren)
            ->orderBy('legalUnitPayloadHistory.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // TODO rajouter ignore
    public function searchCompanyBySiren(SearchSirenFilters $filters, ?array $sorting, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('legalUnitPayloadHistory')
            ->where('legalUnitPayloadHistory.version = (
                SELECT MAX(legalUnitPayloadHistory2.version)
                FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory legalUnitPayloadHistory2
                WHERE legalUnitPayloadHistory2.idInstance = legalUnitPayloadHistory.idInstance
            )')
            ->setMaxResults($limit);

        if (null !== $filters->siren && ContainsOperator::opContains === $filters->siren->operator) {
            $qb->andWhere('legalUnitPayloadHistory.siren LIKE :siren')
                ->setParameter('siren', '%'.$filters->siren->siren.'%');
        }

        if (null !== $filters->businessName && ContainsOperator::opContains === $filters->businessName->operator) {
            $qb->andWhere('legalUnitPayloadHistory.businessName LIKE :businessName')
                ->setParameter('businessName', '%'.$filters->businessName->businessName.'%');
        }

        if (null !== $filters->entityType && StrictOperator::opStrict === $filters->entityType->operator) {
            $qb->andWhere('legalUnitPayloadHistory.entityType = :entityType')
                ->setParameter('entityType', $filters->entityType->entityType);
        }

        if (null !== $filters->administrativeStatus && StrictOperator::opStrict === $filters->administrativeStatus->operator) {
            $qb->andWhere('legalUnitPayloadHistory.administrativeStatus = :administrativeStatus')
                ->setParameter('administrativeStatus', $filters->administrativeStatus->administrativeStatus);
        }

        foreach ($sorting as $sort) {
            if (Order::ascending === $sort->order) {
                $qb->addOrderBy('legalUnitPayloadHistory.'.$sort->field, 'ASC');
            } elseif (Order::descending === $sort->order) {
                $qb->addOrderBy('legalUnitPayloadHistory.'.$sort->field, 'DESC');
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function getMaxVersion(int $idInstance): int
    {
        return (int) $this->createQueryBuilder('legalUnitPayloadHistory')
            ->select('MAX(legalUnitPayloadHistory.version)')
            ->where('legalUnitPayloadHistory.idInstance = :id')
            ->setParameter('id', $idInstance)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
