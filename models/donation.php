<?php

namespace Models;

class Donate
{
    private ?int $id;
    private string $user_identifier; 
    private float $amount;
    private string $payment_method;
    private string $donation_date;

    public function __construct(
        ?int $id,
        string $user_identifier,
        float $amount,
        string $payment_method,
        string $donation_date
    ) {
        $this->id = $id;
        $this->user_identifier = $user_identifier;
        $this->amount = $amount;
        $this->payment_method = $payment_method;
        $this->donation_date = $donation_date;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->user_identifier;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }

    public function getDonationDate(): string
    {
        return $this->donation_date;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUserIdentifier(string $user_identifier): void
    {
        $this->user_identifier = $user_identifier;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function setPaymentMethod(string $payment_method): void
    {
        $this->payment_method = $payment_method;
    }

    public function setDonationDate(string $donation_date): void
    {
        $this->donation_date = $donation_date;
    }
}
