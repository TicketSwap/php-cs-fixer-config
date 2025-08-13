<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig;

final readonly class RuleSet
{
    public function __construct(
        public Fixers $customFixers,
        public string $name,
        public Rules $rules,
    ) {}

    /**
     * Returns a new rule set with custom fixers.
     */
    public function withCustomFixers(Fixers $customFixers) : self
    {
        return new self(
            $this->customFixers->merge($customFixers),
            $this->name,
            $this->rules,
        );
    }

    /**
     * Returns a new rule set with merged rules.
     */
    public function withRules(Rules $rules) : self
    {
        return new self(
            $this->customFixers,
            $this->name,
            $this->rules->merge($rules),
        );
    }
}
