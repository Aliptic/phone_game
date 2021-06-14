<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, options={"default": "pending"})
     */
    private $state;

    /**
     * @ORM\Column(type="json")
     */
    private $users_id = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $room_token;

    /**
     * @ORM\Column(type="integer")
     */
    private $invite_expiration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getUsersId(): ?array
    {
        return $this->users_id;
    }

    public function setUsersId(array $users_id): self
    {
        $this->users_id = $users_id;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getRoomToken(): ?string
    {
        return $this->room_token;
    }

    public function setRoomToken(string $room_token): self
    {
        $this->room_token = $room_token;

        return $this;
    }

    public function getInviteExpiration(): ?int
    {
        return $this->invite_expiration;
    }

    public function setInviteExpiration(int $invite_expiration): self
    {
        $this->invite_expiration = $invite_expiration;

        return $this;
    }
}
