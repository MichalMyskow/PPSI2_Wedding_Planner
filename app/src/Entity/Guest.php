<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GuestRepository::class)
 * @ORM\Table(name="guest")
 */
class Guest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $lastName;

    /**
     * @ORM\Column(name="acceptation", type="boolean", nullable=false)
     * @Assert\Type(type="bool")
     * @Assert\NotNull()
     */
    private bool $acceptation = false;

    /**
     * @ORM\Column(name="seat_number", type="integer", nullable=true)
     */
    private ?int $seatNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Wedding::class, inversedBy="guests")
     * @ORM\JoinColumn(nullable=false)
     */
    private Wedding $wedding;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAcceptation(): bool
    {
        return $this->acceptation;
    }

    public function setAcceptation(bool $acceptation): self
    {
        $this->acceptation = $acceptation;

        return $this;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?int $seatNumber): self
    {
        $this->seatNumber = $seatNumber;

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
