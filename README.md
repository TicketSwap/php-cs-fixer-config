# TicketSwap PHP CS Fixer Config

TicketSwap code style rules for PHP CS Fixer.

## Installation

Install the package via Composer:

```bash
composer require --dev ticketswap/php-cs-fixer-config
```

## Configuration

Create a [`.php-cs-fixer.php`](.php-cs-fixer.php) file in the root of your project:

<!-- source: .php-cs-fixer.php -->
```php
<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use Ticketswap\PhpCsFixerConfig\PhpCsFixerConfigFactory;
use Ticketswap\PhpCsFixerConfig\RuleSet\TicketSwapRuleSet;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->append([__DIR__ . '/.php-cs-fixer.php']);

return PhpCsFixerConfigFactory::create(TicketSwapRuleSet::create())->setFinder($finder);
```

Adjust the paths in the `Finder` to match your project structure.

## Usage

Run PHP CS Fixer to fix your code style:

```bash
vendor/bin/php-cs-fixer fix
```

To check for violations without fixing:

```bash
vendor/bin/php-cs-fixer check --diff
```
