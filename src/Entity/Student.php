<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ine;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isGirl;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModif;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLead;

    /**
     * @ORM\ManyToOne(targetEntity=Classgroup::class, inversedBy="students")
     */
    private $classgroup;

    /**
     * @ORM\Column(type="integer")
     */
    private $xp;

    /**
     * @ORM\Column(type="integer")
     */
    private $gold;

    /**
     * @ORM\Column(type="integer")
     */
    private $elixir;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="students")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Point::class, mappedBy="student", orphanRemoval=true)
     */
    private $points;

    public function __construct()
    {
        $this->parent = new ArrayCollection();
        $this->points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getIne(): ?string
    {
        return $this->ine;
    }

    public function setIne(string $ine): self
    {
        $this->ine = $ine;

        return $this;
    }

    public function getIsGirl(): ?bool
    {
        return $this->isGirl;
    }

    public function setIsGirl(bool $isGirl): self
    {
        $this->isGirl = $isGirl;

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

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(?\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getIsLead(): ?bool
    {
        return $this->isLead;
    }

    public function setIsLead(bool $isLead): self
    {
        $this->isLead = $isLead;

        return $this;
    }

    public function getClassgroup(): ?Classgroup
    {
        return $this->classgroup;
    }

    public function setClassgroup(?Classgroup $classgroup): self
    {
        $this->classgroup = $classgroup;

        return $this;
    }

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function setXp(int $xp): self
    {
        $this->xp = $xp;

        return $this;
    }

    public function getGold(): ?int
    {
        return $this->gold;
    }

    public function setGold(int $gold): self
    {
        $this->gold = $gold;

        return $this;
    }

    public function getElixir(): ?int
    {
        return $this->elixir;
    }

    public function setElixir(int $elixir): self
    {
        $this->elixir = $elixir;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParent(): Collection
    {
        return $this->parent;
    }

    public function addParent(User $parent): self
    {
        if (!$this->parent->contains($parent)) {
            $this->parent[] = $parent;
        }

        return $this;
    }

    public function removeParent(User $parent): self
    {
        if ($this->parent->contains($parent)) {
            $this->parent->removeElement($parent);
        }

        return $this;
    }

    /**
     * @return Collection|Point[]
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): self
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
            $point->setStudent($this);
        }

        return $this;
    }

    public function removePoint(Point $point): self
    {
        if ($this->points->contains($point)) {
            $this->points->removeElement($point);
            // set the owning side to null (unless already changed)
            if ($point->getStudent() === $this) {
                $point->setStudent(null);
            }
        }

        return $this;
    }
}
