<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use Ticketswap\PhpCsFixerConfig\ConfigFactory;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->append([__DIR__ . '/.php-cs-fixer.php']);

return ConfigFactory::create()->setFinder($finder);
