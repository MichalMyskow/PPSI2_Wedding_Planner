<?php

namespace App\Entity;

use App\Repository\GuestConflictRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GuestConflictRepository::class)
 * @ORM\Table(name="guest_conflict")
 */
class GuestConflict
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
