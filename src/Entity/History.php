<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\HistoryRepository;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $game_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="json")
     */
    private $history = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameId(): ?int
    {
        return $this->game_id;
    }

    public function setGameId(int $game_id): self
    {
        $this->game_id = $game_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getHistory(): ?array
    {
        return $this->history;
    }

    public function setHistory(array $history): self
    {
        $this->history = $history;

        return $this;
    }
}
