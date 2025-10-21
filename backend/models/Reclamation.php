<?php

namespace Models;

class Reclamation
{
    private ?int $id = null;
    private int $user_id;
    private string $titre;
    private ?string $description = null;
    private string $type;
    private string $statut = 'pending';
    private ?string $date_reclamation = null;

    // getters/setters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $v): void
    {
        $this->id = $v;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function setUserId(int $v): void
    {
        $this->user_id = $v;
    }
    public function getTitre(): string
    {
        return $this->titre;
    }
    public function setTitre(string $v): void
    {
        $this->titre = $v;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $v): void
    {
        $this->description = $v;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $v): void
    {
        $this->type = $v;
    }
    public function getStatut(): string
    {
        return $this->statut;
    }
    public function setStatut(string $v): void
    {
        $this->statut = $v;
    }
    public function getDateReclamation(): ?string
    {
        return $this->date_reclamation;
    }
    public function setDateReclamation(?string $v): void
    {
        $this->date_reclamation = $v;
    }
}
