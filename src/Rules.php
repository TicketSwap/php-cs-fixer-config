<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig;

final readonly class Rules
{
    /**
     * @param array<string, array<string, mixed>|bool> $value
     */
    public function __construct(
        public array $value,
    ) {}

    public function merge(self $other) : self
    {
        return new self(array_merge(
            $this->value,
            $other->value,
        ));
    }
}
