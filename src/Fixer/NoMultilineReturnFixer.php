<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig\Fixer;

use Override;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class NoMultilineReturnFixer extends AbstractFixer
{
    #[Override]
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The `return` keyword and the expression it returns must be on the same line.',
            [
                new CodeSample(
                    <<<'EOL'
                        <?php

                        function foo()
                        {
                            return
                                $bar->baz();
                        }

                        EOL,
                ),
            ],
        );
    }

    /**
     * Must run before ArrayIndentationFixer.
     */
    #[Override]
    public function getPriority() : int
    {
        return 30;
    }

    #[Override]
    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(T_RETURN);
    }

    #[Override]
    protected function applyFix(SplFileInfo $file, Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ( ! $tokens[$index]->isGivenKind(T_RETURN)) {
                continue;
            }

            $nextIndex = $index + 1;

            if ( ! isset($tokens[$nextIndex])) {
                continue;
            }

            if ( ! $tokens[$nextIndex]->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $whitespace = $tokens[$nextIndex]->getContent();

            if ( ! str_contains($whitespace, "\n")) {
                continue;
            }

            $nextMeaningfulIndex = $tokens->getNextMeaningfulToken($index);

            if ($nextMeaningfulIndex === null || $tokens[$nextMeaningfulIndex]->equals(';')) {
                continue;
            }

            $tokens[$nextIndex] = new Token([T_WHITESPACE, ' ']);
        }
    }
}
