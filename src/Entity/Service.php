<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 */
class Service
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=Checkup::class, mappedBy="services")
     */
    private $checkups;

    public function __construct()
    {
        $this->checkups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Checkup[]
     */
    public function getCheckups(): Collection
    {
        return $this->checkups;
    }

    public function addCheckup(Checkup $checkup): self
    {
        if (!$this->checkups->contains($checkup)) {
            $this->checkups[] = $checkup;
            $checkup->addService($this);
        }

        return $this;
    }

    public function removeCheckup(Checkup $checkup): self
    {
        if ($this->checkups->removeElement($checkup)) {
            $checkup->removeService($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
