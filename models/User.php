<?php
class User {
    private ?int $id = null;
    private string $username;
    private string $email;
    private string $role;
    private ?string $password = null;

    public function __construct(?int $id, string $username, string $email, string $role, ?string $password = null) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }
}
?>
