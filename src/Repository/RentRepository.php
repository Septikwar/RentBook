<?php

namespace App\Repository;

use App\Entity\Rent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Rent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rent[]    findAll()
 * @method Rent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RentRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Rent::class));
    }

    public function findAllRents(int $pagesize = 10, int $page = 1)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('r', 'b', 're')
            ->from(Rent::class, 'r')
            ->leftJoin('r.book', 'b')
            ->leftJoin('r.renter', 're')
            ->setFirstResult($pagesize * ($page - 1))
            ->setMaxResults($pagesize);

        return $qb->getQuery()->getArrayResult();
    }

    public function deleteRent($id)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete(Rent::class, 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->execute();
    }
}
