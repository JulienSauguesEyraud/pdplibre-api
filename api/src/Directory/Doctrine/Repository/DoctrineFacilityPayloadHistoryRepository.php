<?php

declare(strict_types=1);

namespace App\Directory\Doctrine\Repository;

use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Enum\ContainsOperator;
use App\Directory\Enum\Order;
use App\Directory\Enum\StrictOperator;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Repository\FacilityPayloadHistoryRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacilityPayloadHistory>
 */
final class DoctrineFacilityPayloadHistoryRepository extends ServiceEntityRepository implements FacilityPayloadHistoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacilityPayloadHistory::class);
    }

    public function getFacilityById(int $id): ?FacilityPayloadHistory
    {
        return $this->createQueryBuilder('FacilityPayloadHistory')
            ->where('FacilityPayloadHistory.idInstance = :id')
            ->setParameter('id', $id)
            ->orderBy('FacilityPayloadHistory.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getFacilityBySiret(string $siret): ?FacilityPayloadHistory
    {
        return $this->createQueryBuilder('FacilityPayloadHistory')
            ->where('FacilityPayloadHistory.siret = :siret')
            ->setParameter('siret', $siret)
            ->orderBy('FacilityPayloadHistory.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // TODO rajouter ignore
    // TODO rajouter include
    public function searchFacilityBySiret(SearchSiretFilters $filters, ?array $sorting, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('FacilityPayloadHistory')
            ->leftJoin('FacilityPayloadHistory.address', 'address')
            ->where('FacilityPayloadHistory.version = (
                SELECT MAX(FacilityPayloadHistory2.version)
                FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory FacilityPayloadHistory2
                WHERE FacilityPayloadHistory2.idInstance = FacilityPayloadHistory.idInstance
            )')
            ->setMaxResults($limit);

        if (null !== $filters->siret && ContainsOperator::opContains === $filters->siret->operator) {
            $qb->andWhere('FacilityPayloadHistory.siret LIKE :siret')
                ->setParameter('siret', '%'.$filters->siret->siret.'%');
        }

        if (null !== $filters->siren && ContainsOperator::opContains === $filters->siren->operator) {
            $qb->andWhere('FacilityPayloadHistory.siren LIKE :siren')
                ->setParameter('siren', '%'.$filters->siren->siren.'%');
        }

        if (null !== $filters->name && ContainsOperator::opContains === $filters->name->operator) {
            $qb->andWhere('FacilityPayloadHistory.name LIKE :name')
                ->setParameter('name', '%'.$filters->name->name.'%');
        }

        if (null !== $filters->facilityType && ContainsOperator::opContains === $filters->facilityType->operator) {
            $qb->andWhere('FacilityPayloadHistory.facilityType IN (:facilityType)')
                ->setParameter('facilityType', $filters->facilityType->entityType);
        }

        if (null !== $filters->administrativeStatus && StrictOperator::opStrict === $filters->administrativeStatus->operator) {
            $qb->andWhere('FacilityPayloadHistory.administrativeStatus = :administrativeStatus')
                ->setParameter('administrativeStatus', $filters->administrativeStatus->administrativeStatus);
        }

        if (null !== $filters->addressLines && ContainsOperator::opContains === $filters->addressLines->operator) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        'address.addressLine1 LIKE :addressLines',
                        'address.addressLine2 LIKE :addressLines',
                        'address.addressLine3 LIKE :addressLines',
                    )
                )
                ->setParameter('addressLines', '%'.$filters->addressLines->addressLines.'%');
        }

        if (null !== $filters->postalCode && ContainsOperator::opContains === $filters->postalCode->operator) {
            $qb
                ->andWhere('address.postalCode LIKE :postalCode')
                ->setParameter('postalCode', '%'.$filters->postalCode->postalCode.'%');
        }

        if (null !== $filters->countrySubdivision && ContainsOperator::opContains === $filters->countrySubdivision->operator) {
            $qb
                ->andWhere('address.countrySubdivision LIKE :countrySubdivision')
                ->setParameter('countrySubdivision', '%'.$filters->countrySubdivision->countrySubdivision.'%');
        }

        if (null !== $filters->locality && ContainsOperator::opContains === $filters->locality->operator) {
            $qb
                ->andWhere('address.locality LIKE :locality')
                ->setParameter('locality', '%'.$filters->locality->locality.'%');
        }

        foreach ($sorting as $sort) {
            if (Order::ascending === $sort->order) {
                $qb->addOrderBy('FacilityPayloadHistory.'.$sort->field, 'ASC');
            } elseif (Order::descending === $sort->order) {
                $qb->addOrderBy('FacilityPayloadHistory.'.$sort->field, 'DESC');
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function getMaxVersion(int $idInstance): int
    {
        return (int) $this->createQueryBuilder('FacilityPayloadHistory')
            ->select('MAX(FacilityPayloadHistory.version)')
            ->where('FacilityPayloadHistory.idInstance = :id')
            ->setParameter('id', $idInstance)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
