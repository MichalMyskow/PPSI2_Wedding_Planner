<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use App\Validator as WeddingAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GuestRepository::class)
 * @ORM\Table(name="guest")
 * @WeddingAssert\RoomFilled()
 */
class Guest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var Uuid
     *
     * @ORM\Column(name="uuid", type="uuid", nullable=false, unique=true)
     */
    private $uuid;

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

    /**
     * @ORM\ManyToMany(targetEntity=Guest::class, cascade={"merge"})
     * @ORM\JoinTable(name="conflicted_guests",
     *      joinColumns={@ORM\JoinColumn(name="guest_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="conflicted_guest_id", referencedColumnName="id")}
     *      )
     */
    private $conflictedGuests;

    /**
     * @ORM\Column(name="invitation_sent", type="boolean", nullable=false)
     * @Assert\Type(type="bool")
     * @Assert\NotNull()
     */
    private bool $invitationSent = false;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->conflictedGuests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
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

    /**
     * @return Collection|self[]
     */
    public function getConflictedGuests(): Collection
    {
        return $this->conflictedGuests;
    }

    public function addConflictedGuest(Guest $guest): void
    {
        if (!$this->conflictedGuests->contains($guest)) {
            $this->conflictedGuests->add($guest);
            $guest->addConflictedGuest($this);
        }
    }

    public function removeConflictedGuest(Guest $guest): void
    {
        if ($this->conflictedGuests->contains($guest)) {
            $this->conflictedGuests->removeElement($guest);
            $guest->removeConflictedGuest($this);
        }
    }

    public function getInvitationSent(): ?bool
    {
        return $this->invitationSent;
    }

    public function setInvitationSent(bool $invitationSent): self
    {
        $this->invitationSent = $invitationSent;

        return $this;
    }
}
