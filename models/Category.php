<?php

namespace Models;

class Category
{
    private ?int $id = null;
    private string $category_name;
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $v): void
    {
        $this->id = $v;
    }
    public function getCategoryName(): string
    {
        return $this->category_name;
    }
    public function setCategoryName(string $v): void
    {
        $this->category_name = $v;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $v): void
    {
        $this->description = $v;
    }
}
