<?php

namespace Gt\Catalog\Data;

class TokenHolder
{
    private string $token;
    private int $priority;

    public function __construct(string $token, int $priority)
    {
        $this->token = $token;
        $this->priority = $priority;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}