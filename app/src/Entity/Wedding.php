<?php

namespace App\Entity;

use App\Repository\WeddingRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(name="date", type="datetimetz", nullable=false)
     */
    private DateTimeInterface $date;

    /**
     * @ORM\Column(name="bride_first_name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $brideFirstName;

    /**
     * @ORM\Column(name="bride_last_name",type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $brideLastName;

    /**
     * @ORM\Column(name="groom_first_name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $groomFirstName;

    /**
     * @ORM\Column(name="groom_last_name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private string $groomLastName;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="wedding")
     */
    private User $owner;

    /**
     * @ORM\OneToMany(targetEntity=Cost::class, mappedBy="wedding", orphanRemoval=true)
     */
    private $costs;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="wedding", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity=Guest::class, mappedBy="wedding", orphanRemoval=true)
     */
    private $guests;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="weddings")
     * @ORM\JoinColumn(name="room_id", nullable=false)
     */
    private Room $room;

    public function __construct()
    {
        $this->costs = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->guests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        if ($owner->getWedding() !== $this) {
            $owner->setWedding($this);
        }

        return $this;
    }

    public function getBrideFirstName(): string
    {
        return $this->brideFirstName;
    }

    public function setBrideFirstName(string $brideFirstName): self
    {
        $this->brideFirstName = $brideFirstName;

        return $this;
    }

    public function getBrideLastName(): string
    {
        return $this->brideLastName;
    }

    public function setBrideLastName(string $brideLastName): self
    {
        $this->brideLastName = $brideLastName;

        return $this;
    }

    public function getGroomFirstName(): string
    {
        return $this->groomFirstName;
    }

    public function setGroomFirstName(string $groomFirstName): self
    {
        $this->groomFirstName = $groomFirstName;

        return $this;
    }

    public function getGroomLastName(): string
    {
        return $this->groomLastName;
    }

    public function setGroomLastName(string $groomLastName): self
    {
        $this->groomLastName = $groomLastName;

        return $this;
    }

    /**
     * @return Collection|Cost[]
     */
    public function getCosts(): Collection
    {
        return $this->costs;
    }

    public function addCost(Cost $cost): self
    {
        if (!$this->costs->contains($cost)) {
            $this->costs[] = $cost;
            $cost->setWedding($this);
        }

        return $this;
    }

    public function removeCost(Cost $cost): self
    {
        if ($this->costs->removeElement($cost)) {
            // set the owning side to null (unless already changed)
            if ($cost->getWedding() === $this) {
                $cost->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setWedding($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getWedding() === $this) {
                $task->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Guest[]
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guests->contains($guest)) {
            $this->guests[] = $guest;
            $guest->setWedding($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getWedding() === $this) {
                $guest->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @param Room $room
     * @return Wedding
     */
    public function setRoom(Room $room): self
    {
        $this->room = $room;
        return $this;
    }

}
