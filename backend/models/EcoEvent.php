<?php

namespace Models;

class EcoEvent
{
    private ?int $id = null;
    private string $event_name;
    private ?string $description = null;
    private string $ville;
    private string $pays;
    private int $category_id;
    private int $user_id;
    private string $event_date;
    private ?int $participant_limit = null;
    private string $status = 'pending';
    private ?string $created_at = null;

    // getters/setters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $v): void
    {
        $this->id = $v;
    }
    public function getEventName(): string
    {
        return $this->event_name;
    }
    public function setEventName(string $v): void
    {
        $this->event_name = $v;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $v): void
    {
        $this->description = $v;
    }
    public function getVille(): string
    {
        return $this->ville;
    }
    public function setVille(string $v): void
    {
        $this->ville = $v;
    }
    public function getPays(): string
    {
        return $this->pays;
    }
    public function setPays(string $v): void
    {
        $this->pays = $v;
    }
    public function getCategoryId(): int
    {
        return $this->category_id;
    }
    public function setCategoryId(int $v): void
    {
        $this->category_id = $v;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function setUserId(int $v): void
    {
        $this->user_id = $v;
    }
    public function getEventDate(): string
    {
        return $this->event_date;
    }
    public function setEventDate(string $v): void
    {
        $this->event_date = $v;
    }
    public function getParticipantLimit(): ?int
    {
        return $this->participant_limit;
    }
    public function setParticipantLimit(?int $v): void
    {
        $this->participant_limit = $v;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $v): void
    {
        $this->status = $v;
    }
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    public function setCreatedAt(?string $v): void
    {
        $this->created_at = $v;
    }
}
