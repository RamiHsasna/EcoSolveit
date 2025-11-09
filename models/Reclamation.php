<?php
namespace Models;

class Reclamation {
    private $id;
    private $user_id;
    private $user_name;
    private $email;
    private $subject;
    private $message;
    private $statut;
    private $date_reclamation;

    // Setters
    public function setId(int $v) { $this->id = $v; }
    public function setUserId(int $v) { $this->user_id = $v; }
    public function setUserName(string $v) { $this->user_name = $v; }
    public function setEmail(string $v) { $this->email = $v; }         // ← à ajouter
    public function setSubject(string $v) { $this->subject = $v; }
    public function setMessage(string $v) { $this->message = $v; }
    public function setStatut(string $v) { $this->statut = $v; }
    public function setDateReclamation(string $v) { $this->date_reclamation = $v; }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getUserName() { return $this->user_name; }
    public function getEmail() { return $this->email; }                 // ← à ajouter
    public function getSubject() { return $this->subject; }
    public function getMessage() { return $this->message; }
    public function getStatut() { return $this->statut; }
    public function getDateReclamation() { return $this->date_reclamation; }
}
