<?php

namespace App;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\AbstractFixer;
use SplFileInfo;

class ChangePropertyVisibilityFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    use ResolvesVisibilityProperties;

    /**
     * Get the name of the fixer.
     */
    public function getName(): string
    {
        return 'Unfinalize/change_property_visibility';
    }

    /**
     * Get the definition of the fixer.
     */
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Changes the visibility of private properties.',
            [new CodeSample("<?php class Sample { private \$example; }\n")]
        );
    }

    /**
     * Determine if the fixer is a candidate for given Tokens collection.
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_PRIVATE, T_VARIABLE]);
    }

    /**
     * Apply the changes to the file.
     */
    public function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $properties = $this->getVisibilityProperties(
            $this->configuration['visibility'] ?? 'NULL'
        );

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_PRIVATE)) {
                $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];

                if ($nextToken->isGivenKind(T_VARIABLE)) {
                    $tokens[$index] = new Token($properties);
                }
            }
        }
    }

    /**
     * Get the priority of the fixer.
     */
    public function getPriority(): int
    {
        return 0;
    }
}