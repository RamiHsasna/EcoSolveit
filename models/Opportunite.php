<?php
class Opportunite {
    private ?int $id = null;
    private string $title;
    private string $description;
    private string $date;

    public function __construct(?int $id, string $title, string $description, string $date) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setDate(string $date): void {
        $this->date = $date;
    }
}
?>
