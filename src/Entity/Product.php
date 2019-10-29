<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPaid;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastEdit;

    /**
     * @ORM\Column(type="date")
     */
    private $forMonth;

    /**
     * @ORM\Column(type="integer")
     */

    private $feeInDays;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="products")
     */
    private $students;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", mappedBy="products")
     */
    //@ORM\OneToOne(targetEntity="App\Entity\Payment", mappedBy="products", cascade={"persist", "remove"})
    private $payment;

    public function __construct()
    {
        $datetime = new \DateTime('now');
        $this->forMonth = $datetime;
        $this->dateCreate = $datetime;
        $this->lastEdit = $datetime;
        $this->isPaid = false;
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTimeInterface $dateCreate): self
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getLastEdit(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setLastEdit(\DateTimeInterface $lastEdit): self
    {
        $this->lastEdit = $lastEdit;

        return $this;
    }

    public function getFeeInDays(): ?int
    {
        return $this->feeInDays;
    }

    public function setFeeInDays(int $feeInDays): self
    {
        $this->feeInDays = $feeInDays;

        return $this;
    }

    public function getForMonth(): ?\DateTimeInterface
    {
        return $this->forMonth;
    }

    public function setForMonth(\DateTimeInterface $forMonth): self
    {
        $this->forMonth = $forMonth;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->students;
    }

    public function setStudent(?Student $student): self
    {
        $this->students = $student;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        // set (or unset) the owning side of the relation if necessary
        $newProducts = $payment === null ? null : $this;
        if ($newProducts !== $payment->getProducts()) {
            $payment->setProducts($newProducts);
        }

        return $this;
    }
}
