<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig\RuleSet;

use Ticketswap\PhpCsFixerConfig\Fixers;
use Ticketswap\PhpCsFixerConfig\Rules;
use Ticketswap\PhpCsFixerConfig\RuleSet;

final class TicketSwapTestingStyle
{
    public static function create() : RuleSet
    {
        return new RuleSet(
            new Fixers(),
            'TicketSwap Testing Style',
            new Rules([
                'php_unit_method_casing' => [
                    'case' => 'snake_case',
                ],
                'php_unit_test_annotation' => [
                    'style' => 'annotation',
                ],
            ]),
        );
    }
}
