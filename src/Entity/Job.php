<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Classgroup::class, inversedBy="jobs")
     */
    private $classgroup;

    /**
     * @ORM\OneToMany(targetEntity=Occupation::class, mappedBy="job")
     */
    private $occupations;

    public function __construct()
    {
        $this->occupations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection|Occupation[]
     */
    public function getOccupations(): Collection
    {
        return $this->occupations;
    }

    public function addOccupation(Occupation $occupation): self
    {
        if (!$this->occupations->contains($occupation)) {
            $this->occupations[] = $occupation;
            $occupation->setJob($this);
        }

        return $this;
    }

    public function removeOccupation(Occupation $occupation): self
    {
        if ($this->occupations->contains($occupation)) {
            $this->occupations->removeElement($occupation);
            // set the owning side to null (unless already changed)
            if ($occupation->getJob() === $this) {
                $occupation->setJob(null);
            }
        }

        return $this;
    }
}
