<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PetRepository::class)
 */
class Pet
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
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sex;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="pets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=AnimalKind::class, inversedBy="pets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $kind;

    /**
     * @ORM\OneToMany(targetEntity=Checkup::class, mappedBy="pet")
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getSex(): ?bool
    {
        return $this->sex;
    }

    public function setSex(bool $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getOwner(): ?Client
    {
        return $this->owner;
    }

    public function setOwner(?Client $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getKind(): ?AnimalKind
    {
        return $this->kind;
    }

    public function setKind(?AnimalKind $kind): self
    {
        $this->kind = $kind;

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
            $checkup->setPet($this);
        }

        return $this;
    }

    public function removeCheckup(Checkup $checkup): self
    {
        if ($this->checkups->removeElement($checkup)) {
            // set the owning side to null (unless already changed)
            if ($checkup->getPet() === $this) {
                $checkup->setPet(null);
            }
        }

        return $this;
    }
}
