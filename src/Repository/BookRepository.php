<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Book::class));
    }

    public function findAllBooks(int $pagesize = 10, int $page = 1)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('b')
            ->from(Book::class, 'b')
            ->setFirstResult($pagesize * ($page - 1))
            ->setMaxResults($pagesize);

        return $qb->getQuery()->getArrayResult();
    }

    public function deleteBook($id)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete(Book::class, 'b')
            ->where('b.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->execute();
    }
}
