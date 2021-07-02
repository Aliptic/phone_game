<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    // The 4 different states a game could be
    const STATE = [
        0 => 'Pending',
        1 => 'Canceled',
        2 => 'Ongoing',
        3 => 'Finished',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, options={"default": "Pending"})
     */
    private $state = self::STATE[0];

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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VoteSentence;

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

    public function getVoteSentence(): ?string
    {
        return $this->VoteSentence;
    }

    public function setVoteSentence(?string $VoteSentence): self
    {
        $this->VoteSentence = $VoteSentence;

        return $this;
    }
}
