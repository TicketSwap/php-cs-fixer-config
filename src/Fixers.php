<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig;

use PhpCsFixer\Fixer\FixerInterface;

final readonly class Fixers
{
    /**
     * @var list<FixerInterface>
     */
    public array $value;

    public function __construct(
        FixerInterface ...$value,
    ) {
        $this->value = array_values($value);
    }

    public function merge(self $customFixers) : self
    {
        return new self(...$this->value, ...$customFixers->value);
    }
}
