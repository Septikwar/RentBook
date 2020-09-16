<?php

namespace App\Service;

use App\Entity\Rent;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class RentService
{
    /**
     * @var BookRepository
     */
    private $bookRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $this->bookRepository = $bookRepository;
        $this->em = $entityManager;
    }

    /**
     * @param Rent $rent
     * @return bool
     * @throws \Exception
     */
    public function checkSumToRent(Rent $rent)
    {
        $requiredSum = (int) $rent->getQuantity() * (int) $rent->getDays() * (float) $rent->getBook()->getPrice() * 0.70;
        $renterSum = (int) $rent->getDays() * (float) $rent->getSum();

        if ($requiredSum >= $renterSum) {
            throw new \Exception('Ваша сумма залога меньше, чем 70% стоимости аренды книги', 400);
        }

        return true;
    }

    /**
     * @param Rent $rent
     * @return Rent
     * @throws \Exception
     */
    public function substractQuantity(Rent $rent)
    {
        $quantityBooks = $rent->getBook()->getQuantity();
        $quantityToRent = $rent->getQuantity();

        $actualQuantityBooks = $quantityBooks - $quantityToRent;

        if ($actualQuantityBooks <= 0) {
            throw new \Exception('Книг больше не осталось', 400);
        }

        $rent->getBook()->setQuantity($actualQuantityBooks);

        return $rent;
    }

    /**
     * @param Rent $rent
     * @return bool|string
     */
    public function returnBooksToShop(Rent $rent)
    {
        $quantityBooks = $rent->getBook()->getQuantity();
        $quantityInRent = $rent->getQuantity();

        $rent->getBook()->setQuantity((int) $quantityBooks + (int) $quantityInRent);

        try {
            $this->em->persist($rent);
            $this->em->flush();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
}