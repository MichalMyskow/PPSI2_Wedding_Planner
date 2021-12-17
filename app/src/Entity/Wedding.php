<?php

namespace App\Entity;

use App\Repository\WeddingRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=WeddingRepository::class)
 * @ORM\Table(name="wedding")
 */
class Wedding
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="date", type="datetimetz", nullable=true)
     */
    private DateTimeInterface $date;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private string $address;

    /**
     * @ORM\Column(name="bride_first_name", type="string", length=255, nullable=true)
     */
    private string $brideFirstName;

    /**
     * @ORM\Column(name="bride_last_name",type="string", length=255, nullable=true)
     */
    private string $brideLastName;

    /**
     * @ORM\Column(name="groom_first_name", type="string", length=255, nullable=true)
     */
    private string $groomFirstName;

    /**
     * @ORM\Column(name="groom_last_name", type="string", length=255, nullable=true)
     */
    private string $groomLastName;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="wedding")
     * @Assert\NotBlank()
     */
    private User $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        if ($owner->getWedding() !== $this) {
            $owner->setWedding($this);
        }

        $this->owner = $owner;

        return $this;
    }

    public function getBrideFirstName(): ?string
    {
        return $this->brideFirstName;
    }

    public function setBrideFirstName(?string $brideFirstName): self
    {
        $this->brideFirstName = $brideFirstName;

        return $this;
    }

    public function getBrideLastName(): ?string
    {
        return $this->brideLastName;
    }

    public function setBrideLastName(?string $brideLastName): self
    {
        $this->brideLastName = $brideLastName;

        return $this;
    }

    public function getGroomFirstName(): ?string
    {
        return $this->groomFirstName;
    }

    public function setGroomFirstName(?string $groomFirstName): self
    {
        $this->groomFirstName = $groomFirstName;

        return $this;
    }

    public function getGroomLastName(): ?string
    {
        return $this->groomLastName;
    }

    public function setGroomLastName(?string $groomLastName): self
    {
        $this->groomLastName = $groomLastName;

        return $this;
    }
}
