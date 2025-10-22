<?php
class Opportunite {
    private ?int $id = null;
    private string $titre;
    private string $description;

    public function __construct(?int $id, string $titre, string $description) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }

    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function setDescription(string $description): void { $this->description = $description; }
}
?>
