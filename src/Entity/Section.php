<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 */
class Section
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rank;

    /**
     * @ORM\ManyToMany(targetEntity=Classgroup::class, mappedBy="section")
     */
    private $classgroups;

    /**
     * @ORM\OneToMany(targetEntity=Student::class, mappedBy="section")
     */
    private $students;

    public function __construct()
    {
        $this->classgroups = new ArrayCollection();
        $this->students = new ArrayCollection();
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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return Collection|Classgroup[]
     */
    public function getClassgroups(): Collection
    {
        return $this->classgroups;
    }

    public function addClassgroup(Classgroup $classgroup): self
    {
        if (!$this->classgroups->contains($classgroup)) {
            $this->classgroups[] = $classgroup;
            $classgroup->addSection($this);
        }

        return $this;
    }

    public function removeClassgroup(Classgroup $classgroup): self
    {
        if ($this->classgroups->contains($classgroup)) {
            $this->classgroups->removeElement($classgroup);
            $classgroup->removeSection($this);
        }

        return $this;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setSection($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getSection() === $this) {
                $student->setSection(null);
            }
        }

        return $this;
    }
}
