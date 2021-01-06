<?php

namespace App\Entity;

use App\Repository\OccupationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OccupationRepository::class)
 */
class Occupation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="occupations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="occupations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="integer")
     */
    private $salary;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }
}
