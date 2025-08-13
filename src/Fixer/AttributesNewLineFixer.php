<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig\Fixer;

use Override;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * TODO https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/pull/8695 wait for this be merged, and then use the official version.
 */
final class AttributesNewLineFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    #[Override]
    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(T_ATTRIBUTE);
    }

    #[Override]
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Attributes should be on their own line.',
            [
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] #[Bar] class Baz
{
}\n",
                    new VersionSpecification(8_00_00),
                ),
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] class Bar
{
    #[Baz] public function foo() {}
}\n",
                    new VersionSpecification(8_00_00),
                ),
                new VersionSpecificCodeSample(
                    "<?php
#[Foo] class Bar
{
    #[Test] public const TEST = 'Test';
}\n",
                    new VersionSpecification(8_00_00),
                ),
            ],
        );
    }

    #[Override]
    protected function applyFix(SplFileInfo $file, Tokens $tokens) : void
    {
        $count = $tokens->count();
        for ($index = $count - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $this->fixNewline($tokens, $index);
            }
        }
    }

    private function fixNewline(Tokens $tokens, int $endIndex) : void
    {
        $nextIndex = $endIndex + 1;

        if ($tokens[$nextIndex]->isWhitespace()) {
            $whitespace = $tokens[$nextIndex]->getContent();

            if (str_contains($whitespace, "\n") || str_contains($whitespace, "\r")) {
                return;
            }

            $tokens->clearAt($nextIndex);
        }

        $indentation = $this->getIndentation($tokens, $endIndex);

        $tokens->ensureWhitespaceAtIndex(
            $endIndex + 1,
            0,
            $this->whitespacesConfig->getLineEnding() . $indentation,
        );
    }

    private function getIndentation(Tokens $tokens, int $attributeEndIndex) : string
    {
        $nextMeaningfulIndex = $tokens->getNextMeaningfulToken($attributeEndIndex);

        if ($nextMeaningfulIndex === null || $tokens[$nextMeaningfulIndex]->isGivenKind([T_CLASS])) {
            return '';
        }

        $searchIndex = $nextMeaningfulIndex;

        do {
            $prevWhitespaceIndex = $tokens->getPrevTokenOfKind(
                $searchIndex,
                [[T_ENCAPSED_AND_WHITESPACE], [T_INLINE_HTML], [T_WHITESPACE]],
            );

            $searchIndex = $prevWhitespaceIndex;
        } while ($prevWhitespaceIndex !== null
        && ! str_contains($tokens[$prevWhitespaceIndex]->getContent(), "\n")
        );

        if ($prevWhitespaceIndex === null) {
            return '';
        }

        $whitespaceContent = $tokens[$prevWhitespaceIndex]->getContent();

        if (str_contains($whitespaceContent, "\n")) {
            $lastNewLinePos = strrpos($whitespaceContent, "\n");

            if ($lastNewLinePos === false) {
                return '';
            }

            return substr($whitespaceContent, $lastNewLinePos + 1);
        }

        return $whitespaceContent;
    }
}
