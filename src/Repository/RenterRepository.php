<?php

namespace App\Repository;

use App\Entity\Renter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Renter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Renter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Renter[]    findAll()
 * @method Renter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RenterRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Renter::class));
    }

    public function findAllRenters(int $pagesize = 10, int $page = 1)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('r')
            ->from(Renter::class, 'r')
            ->setFirstResult($pagesize * ($page - 1))
            ->setMaxResults($pagesize);

        return $qb->getQuery()->getArrayResult();
    }

    public function deleteRenter($id)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete(Renter::class, 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->execute();
    }
}
