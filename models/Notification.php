<?php

namespace Models;

class Notification
{
    private ?int $id = null;
    private string $type;
    private int $user_id;
    private string $title;
    private string $message;
    private ?string $link = null;
    private bool $is_read = false;
    private ?string $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $v): void
    {
        $this->id = $v;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $v): void
    {
        $this->type = $v;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function setUserId(int $v): void
    {
        $this->user_id = $v;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $v): void
    {
        $this->title = $v;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $v): void
    {
        $this->message = $v;
    }
    public function getLink(): ?string
    {
        return $this->link;
    }
    public function setLink(?string $v): void
    {
        $this->link = $v;
    }
    public function isRead(): bool
    {
        return $this->is_read;
    }
    public function setIsRead(bool $v): void
    {
        $this->is_read = $v;
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
