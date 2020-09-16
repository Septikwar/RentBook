<?php

namespace App\Entity;

use App\Repository\RentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RentRepository::class)
 */
class Rent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Book::class, cascade={"persist", "remove"})
     */
    private $book;

    /**
     * @ORM\OneToOne(targetEntity=Renter::class, cascade={"persist", "remove"})
     */
    private $renter;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private $sum;

    /**
     * @ORM\Column(type="integer")
     */
    private $days;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getRenter(): ?Renter
    {
        return $this->renter;
    }

    public function setRenter(?Renter $renter): self
    {
        $this->renter = $renter;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): self
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * @return integer
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param integer $days
     */
    public function setDays($days): void
    {
        $this->days = $days;
    }
}
