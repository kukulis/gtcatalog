<?php

namespace Gt\Catalog\Services\Rest;

use Gt\Catalog\Data\TokenHolder;
use Psr\Log\LoggerInterface;

class TokensCollector implements IPriorityDecider
{
    /** @var TokenHolder[] */
    private array $tokenHolders = [];

    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function decidePriority(string $token): int
    {

        // TODO remove after testing
        $this->logger->error(
            sprintf('Total tokens: %s', count($this->tokenHolders))
        );

        // --

        foreach ($this->tokenHolders as $holder) {
            if ($token == $holder->getToken()) {
                return $holder->getPriority();
            }
        }

        // TODO remove after testing
        foreach ($this->tokenHolders as $holder) {
            $this->logger->error(
                sprintf('Available token: %s, priority %s', $holder->getToken(), $holder->getPriority())
            );
        }
        //

        return -1;
    }

    public function addTokenHolder(string $token, int $priority): void
    {
        $this->tokenHolders[] = new TokenHolder($token, $priority);
    }
}