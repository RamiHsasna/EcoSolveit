<?php

namespace Models;

class EventRealization
{
    private ?int $id = null;
    private int $event_id;
    private string $status = 'not_started';
    private string $start_date;
    private string $end_date;
    private ?int $success_rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $v): void
    {
        $this->id = $v;
    }
    public function getEventId(): int
    {
        return $this->event_id;
    }
    public function setEventId(int $v): void
    {
        $this->event_id = $v;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $v): void
    {
        $this->status = $v;
    }
    public function getStartDate(): string
    {
        return $this->start_date;
    }
    public function setStartDate(string $v): void
    {
        $this->start_date = $v;
    }
    public function getEndDate(): string
    {
        return $this->end_date;
    }
    public function setEndDate(string $v): void
    {
        $this->end_date = $v;
    }
    public function getSuccessRating(): ?int
    {
        return $this->success_rating;
    }
    public function setSuccessRating(?int $v): void
    {
        $this->success_rating = $v;
    }
}
