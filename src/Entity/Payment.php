<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
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
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $payment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePurchases;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastEdit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $namePayer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $seller;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastEditUser;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Product", inversedBy="payment")
     */
    private $products;

    public function __construct()
    {
        $datetime = new \DateTime('now');
        $this->datePurchases = $datetime;
        $this->lastEdit = $datetime;
        $this->products = new ArrayCollection();
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

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDatePurchases(): ?\DateTimeInterface
    {
        return $this->datePurchases;
    }

    public function setDatePurchases(\DateTimeInterface $datePurchases): self
    {
        $this->datePurchases = $datePurchases;

        return $this;
    }

    public function getLastEdit(): ?\DateTimeInterface
    {
        return $this->lastEdit;
    }

    public function setLastEdit(\DateTimeInterface $lastEdit): self
    {
        $this->lastEdit = $lastEdit;

        return $this;
    }

    public function getNamePayer(): ?string
    {
        return $this->namePayer;
    }

    public function setNamePayer(string $namePayer): self
    {
        $this->namePayer = $namePayer;

        return $this;
    }

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function setSeller(string $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getLastEditUser(): ?string
    {
        return $this->lastEditUser;
    }

    public function setLastEditUser(string $lastEditUser): self
    {
        $this->lastEditUser = $lastEditUser;

        return $this;
    }

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): self
    {
        $this->products = $products;

        return $this;
    }
}
