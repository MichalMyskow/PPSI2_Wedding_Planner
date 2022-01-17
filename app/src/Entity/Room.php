<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $id;

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

    /**
     * @ORM\OneToMany(targetEntity=Wedding::class, mappedBy="room")
     */
    private $weddings;

    /**
     * @ORM\Column(name="street",type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $street;

    /**
     * @ORM\Column(name="house_number", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $houseNumber;

    /**
     * @ORM\Column(name="postcode", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $postcode;

    public function __construct()
    {
        $this->weddings = new ArrayCollection();
    }

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

    /**
     * @return Collection|Wedding[]
     */
    public function getWeddings(): Collection
    {
        return $this->weddings;
    }

    public function addWedding(Wedding $wedding): self
    {
        if (!$this->weddings->contains($wedding)) {
            $this->weddings[] = $wedding;
            $wedding->setRoom($this);
        }

        return $this;
    }

    public function removeWedding(Wedding $wedding): self
    {
        if ($this->weddings->removeElement($wedding)) {
            // set the owning side to null (unless already changed)
            if ($wedding->getRoom() === $this) {
                $wedding->setRoom(null);
            }
        }

        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }
}
