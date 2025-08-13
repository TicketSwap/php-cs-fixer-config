<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig;

use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use RuntimeException;

final class PhpCsFixerConfigFactory
{
    /**
     * Creates a configuration based on a rule set.
     *
     * @throws RuntimeException
     */
    public static function create(RuleSet $ruleSet) : Config
    {
        $config = new Config();

        $config->setUnsupportedPhpVersionAllowed(true);
        $config->registerCustomFixers($ruleSet->customFixers->value);
        $config->setParallelConfig(ParallelConfigFactory::detect());
        $config->setRiskyAllowed(true);
        $config->setRules($ruleSet->rules->value);

        return $config;
    }
}
