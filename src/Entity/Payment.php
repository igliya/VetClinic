<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $sum;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToOne(targetEntity=Checkup::class, inversedBy="payment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checkup;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $registrar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getCheckup(): ?Checkup
    {
        return $this->checkup;
    }

    public function setCheckup(Checkup $checkup): self
    {
        $this->checkup = $checkup;

        return $this;
    }

    public function getRegistrar(): ?User
    {
        return $this->registrar;
    }

    public function setRegistrar(?User $registrar): self
    {
        $this->registrar = $registrar;

        return $this;
    }
}
