<?php

namespace Gt\Catalog\Services\Rest;

interface IPriorityDecider
{
    public function decidePriority(string $token): int;
}