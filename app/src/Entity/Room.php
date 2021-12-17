<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 * @ORM\Table(name="room")
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="size", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private int $size;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $address;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
