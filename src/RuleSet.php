<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig;

final readonly class RuleSet
{
    public Fixers $customFixers;
    public Rules $rules;

    public function __construct(
        Fixers $customFixers,
        Rules $rules,
    ) {
        $this->customFixers = $customFixers;

        $enable = [];
        foreach ($customFixers->value as $customFixer) {
            $enable[$customFixer->getName()] = true;
        }

        $this->rules = new Rules($enable)->merge($rules);
    }

    /**
     * Returns a new rule set with custom fixers.
     */
    public function withCustomFixers(Fixers $customFixers) : self
    {
        return new self(
            $this->customFixers->merge($customFixers),
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
            $this->rules->merge($rules),
        );
    }

    public function merge(self $other) : self
    {
        return new self(
            $this->customFixers->merge($other->customFixers),
            $this->rules->merge($other->rules),
        );
    }
}
