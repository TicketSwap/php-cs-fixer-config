<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Ensures PHPDoc comments are positioned above attributes on class declarations.
 */
final class PhpdocAboveAttributeFixer extends AbstractFixer
{
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHPDoc comment must be positioned above attributes on class declarations.',
            [
                new VersionSpecificCodeSample(
                    <<<'EOL'
                        <?php

                        #[AsAlias]
                        /**
                         * This is a class comment.
                         */
                        class MyClass {}

                        EOL,
                    new VersionSpecification(8_00_00),
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_ATTRIBUTE) && $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoBlankLinesAfterPhpdocFixer.
     */
    public function getPriority() : int
    {
        return 1;
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if ( ! $tokens[$index]->isGivenKind(\T_CLASS)) {
                continue;
            }

            // Find the start of the class declaration (including attributes and PHPDoc)
            $startIndex = $this->findClassDeclarationStart($tokens, $index);

            // Look for PHPDoc and attributes between startIndex and class keyword
            $phpdocIndex = null;
            $firstAttributeIndex = null;

            for ($i = $startIndex; $i < $index; ++$i) {
                if ($tokens[$i]->isGivenKind(\T_DOC_COMMENT)) {
                    $phpdocIndex = $i;
                }

                if ($tokens[$i]->isGivenKind(\T_ATTRIBUTE) && $firstAttributeIndex === null) {
                    $firstAttributeIndex = $i;
                }
            }

            // If we have both PHPDoc and attribute, and PHPDoc comes after attribute, we need to swap
            if ($phpdocIndex !== null && $firstAttributeIndex !== null && $phpdocIndex > $firstAttributeIndex) {
                $this->swapPhpdocAndAttributes($tokens, $phpdocIndex, $firstAttributeIndex, $index);
            }
        }
    }

    private function findClassDeclarationStart(Tokens $tokens, int $classIndex) : int
    {
        $index = $classIndex;

        while ($index > 0) {
            $prevIndex = $tokens->getPrevMeaningfulToken($index - 1);

            if ($prevIndex === null) {
                break;
            }

            // Stop if we hit another class/interface/trait or function
            if ($tokens[$prevIndex]->isGivenKind([\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_FUNCTION, \T_CLOSE_TAG, \T_OPEN_TAG]) || $tokens[$prevIndex]->equals(';')) {
                return $index;
            }

            // Continue past visibility modifiers, abstract, final, readonly
            if ($tokens[$prevIndex]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_ABSTRACT, \T_FINAL, FCT::T_READONLY])) {
                $index = $prevIndex;

                continue;
            }

            // Continue past attributes
            if ($tokens[$prevIndex]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                // Find the opening bracket of the attribute
                $attributeStart = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $prevIndex);
                $index = $attributeStart;

                continue;
            }

            // Continue past PHPDoc
            if ($tokens[$prevIndex]->isGivenKind(\T_DOC_COMMENT)) {
                $index = $prevIndex;

                continue;
            }

            break;
        }

        return $index;
    }

    private function swapPhpdocAndAttributes(Tokens $tokens, int $phpdocIndex, int $firstAttributeIndex, int $classIndex) : void
    {
        // Find the end of the last attribute before the PHPDoc
        $lastAttributeEndIndex = $phpdocIndex - 1;
        while ($lastAttributeEndIndex > $firstAttributeIndex && $tokens[$lastAttributeEndIndex]->isWhitespace()) {
            --$lastAttributeEndIndex;
        }

        if ( ! $tokens[$lastAttributeEndIndex]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
            return; // Safety check
        }

        // Find the end of PHPDoc
        $phpdocEndIndex = $phpdocIndex;
        while ($phpdocEndIndex + 1 < $classIndex && $tokens[$phpdocEndIndex + 1]->isWhitespace()) {
            ++$phpdocEndIndex;
        }

        // Extract PHPDoc block
        $phpdocTokens = [];
        for ($j = $phpdocIndex; $j <= $phpdocEndIndex; ++$j) {
            $phpdocTokens[] = clone $tokens[$j];
        }

        // Extract attribute block (attributes + whitespace after them up to PHPDoc)
        $attributeTokens = [];
        for ($j = $firstAttributeIndex; $j < $phpdocIndex; ++$j) {
            $attributeTokens[] = clone $tokens[$j];
        }

        // Reconstruct in correct order: PHPDoc first, then attributes
        $newTokens = array_merge($phpdocTokens, $attributeTokens);

        // Replace the entire range
        $tokens->overrideRange($firstAttributeIndex, $phpdocEndIndex, $newTokens);
    }
}
