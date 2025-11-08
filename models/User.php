<?php

namespace models;

class User
{
    private ?int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $ville;
    private string $pays;
    private string $user_type = 'user';
    private string $status = 'active';
    private ?string $created_at = null;
    private ?string $resetToken = null;
    private ?string $tokenExpire = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $v): void
    {
        $this->username = $v;
    }

    public function getTokenExpire(): ?string
    {
        return $this->tokenExpire;
    }
    public function setTokenExpire(?string $expire): void
    {
        $this->tokenExpire = $expire;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $v): void
    {
        $this->email = $v;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $v): void
    {
        $this->password = $v;
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

    public function getUserType(): string
    {
        return $this->user_type;
    }
    public function setUserType(string $v): void
    {
        $this->user_type = $v;
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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
    public function setResetToken(?string $token): void
    {
        $this->resetToken = $token;
    }
}
