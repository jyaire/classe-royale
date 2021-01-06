<?php

namespace App\Entity;

use App\Repository\ClassgroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClassgroupRepository::class)
 */
class Classgroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ref;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="classgroups")
     */
    private $school;

    /**
     * @ORM\OneToMany(targetEntity=Student::class, mappedBy="classgroup")
     */
    private $students;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="classgroups")
     */
    private $teacher;

    /**
     * @ORM\ManyToMany(targetEntity=Section::class, inversedBy="classgroups")
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity=Team::class, mappedBy="classgroup")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="classgroup")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="classgroup")
     */
    private $jobs;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->teacher = new ArrayCollection();
        $this->section = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

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
            $student->setClassgroup($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getClassgroup() === $this) {
                $student->setClassgroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getTeacher(): Collection
    {
        return $this->teacher;
    }

    public function addTeacher(User $teacher): self
    {
        if (!$this->teacher->contains($teacher)) {
            $this->teacher[] = $teacher;
        }

        return $this;
    }

    public function removeTeacher(User $teacher): self
    {
        if ($this->teacher->contains($teacher)) {
            $this->teacher->removeElement($teacher);
        }

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSection(): Collection
    {
        return $this->section;
    }

    public function addSection(Section $section): self
    {
        if (!$this->section->contains($section)) {
            $this->section[] = $section;
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->section->contains($section)) {
            $this->section->removeElement($section);
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setClassgroup($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getClassgroup() === $this) {
                $team->setClassgroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setClassgroup($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getClassgroup() === $this) {
                $product->setClassgroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setClassgroup($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
            // set the owning side to null (unless already changed)
            if ($job->getClassgroup() === $this) {
                $job->setClassgroup(null);
            }
        }

        return $this;
    }
}
