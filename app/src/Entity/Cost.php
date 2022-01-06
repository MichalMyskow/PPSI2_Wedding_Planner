<?php

namespace App\Entity;

use App\Repository\CostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CostRepository::class)
 * @ORM\Table(name="cost")
 */
class Cost
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float", nullable=false)
     * @Assert\NotBlank()
     */
    private float $cost;

    /**
     * @ORM\ManyToOne(targetEntity=Wedding::class, inversedBy="costs")
     * @ORM\JoinColumn(nullable=false)
     */
    private Wedding $wedding;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getWedding(): Wedding
    {
        return $this->wedding;
    }

    public function setWedding(Wedding $wedding): self
    {
        $this->wedding = $wedding;

        return $this;
    }
}
