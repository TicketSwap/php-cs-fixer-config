<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig;

use Override;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final readonly class NameWrapper implements FixerInterface
{
    public function __construct(
        private FixerInterface $inner,
    ) {}

    #[Override]
    public function isCandidate(Tokens $tokens) : bool
    {
        return $this->inner->isCandidate($tokens);
    }

    #[Override]
    public function isRisky() : bool
    {
        return $this->inner->isRisky();
    }

    #[Override]
    public function fix(SplFileInfo $file, Tokens $tokens) : void
    {
        $this->inner->fix($file, $tokens);
    }

    #[Override]
    public function getDefinition() : FixerDefinitionInterface
    {
        return $this->inner->getDefinition();
    }

    #[Override]
    public function getName() : string
    {
        return 'Custom/' . str_replace('\\', '_', strtolower($this->inner->getName()));
    }

    #[Override]
    public function getPriority() : int
    {
        return $this->inner->getPriority();
    }

    #[Override]
    public function supports(SplFileInfo $file) : bool
    {
        return $this->inner->supports($file);
    }
}
