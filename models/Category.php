<?php
namespace Models;

class Category
{
    private ?int $id;
    private string $category_name;
    private string $description;

    public function __construct(
        ?int $id,
        string $category_name,
        string $description
    ) {
        $this->id = $id;
        $this->category_name = $category_name;
        $this->description = $description;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getCategoryName(): string { return $this->category_name; }
    public function getDescription(): string { return $this->description; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setCategoryName(string $category_name): void { $this->category_name = $category_name; }
    public function setDescription(string $description): void { $this->description = $description; }
}
?>
