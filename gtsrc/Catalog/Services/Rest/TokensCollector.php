<?php

namespace Gt\Catalog\Services\Rest;

use Gt\Catalog\Data\TokenHolder;

class TokensCollector implements IPriorityDecider
{
    /** @var TokenHolder[] */
    private array $tokenHolders = [];

    public function decidePriority(string $token): int
    {
        foreach ($this->tokenHolders as $holder) {
            if ($token == $holder->getToken()) {
                return $holder->getPriority();
            }
        }

        return -1;
    }

    public function addTokenHolder(string $token, int $priority): void
    {
        $this->tokenHolders[] = new TokenHolder($token, $priority);
    }
}