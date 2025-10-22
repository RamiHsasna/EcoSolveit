<?php

namespace Models;

class EventParticipation
{
    private ?int $id = null;
    private int $event_id;
    private int $user_id;

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
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function setUserId(int $v): void
    {
        $this->user_id = $v;
    }
}
