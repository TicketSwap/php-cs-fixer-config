<?php

declare(strict_types=1);

namespace Ticketswap\PhpCsFixerConfig\RuleSet;

use ErickSkrauch\PhpCsFixer\Fixer\Whitespace\LineBreakAfterStatementsFixer;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLineConstructorParamFixer;
use Symplify\CodingStandard\TokenAnalyzer\Naming\MethodNameResolver;
use Symplify\CodingStandard\TokenAnalyzer\ParamNewliner;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ArrayAnalyzer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\Arrays\ArrayItemNewliner;
use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthCloserTransformer;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthOpenerTransformer;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\Traverser\ArrayBlockInfoFinder;
use Symplify\CodingStandard\TokenRunner\Whitespace\IndentResolver;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;
use Ticketswap\PhpCsFixerConfig\Fixers;
use Ticketswap\PhpCsFixerConfig\NameWrapper;
use Ticketswap\PhpCsFixerConfig\Rules;
use Ticketswap\PhpCsFixerConfig\RuleSet;

final class TicketSwapRuleSet
{
    public static function create() : RuleSet
    {
        $whitespacesFixerConfig = new WhitespacesFixerConfig('    ', "\n");
        $blockfinder = new BlockFinder();
        $tokenSkipper = new TokenSkipper($blockfinder);
        $arrayAnalyzer = new ArrayAnalyzer($tokenSkipper);
        $arrayItemNewliner = new ArrayItemNewliner($arrayAnalyzer, $whitespacesFixerConfig);
        $arrayBlockInfoFinder = new ArrayBlockInfoFinder($blockfinder);
        $arrayWrapperFactory = new ArrayWrapperFactory($tokenSkipper);
        $callAnalyzer = new CallAnalyzer();
        $tokenFinder = new TokenFinder();
        $lineLengthCloserTransformer = new LineLengthCloserTransformer($callAnalyzer, $tokenFinder);
        $lineLengthOpenerTransformer = new LineLengthOpenerTransformer($callAnalyzer);
        $indentDetector = new IndentDetector($whitespacesFixerConfig);
        $indentResolver = new IndentResolver($indentDetector, $whitespacesFixerConfig);
        $tokensNewliner = new TokensNewliner($lineLengthCloserTransformer, $tokenSkipper, $lineLengthOpenerTransformer, $whitespacesFixerConfig, $indentResolver);
        $paramNewliner = new ParamNewliner($blockfinder, $tokensNewliner);
        $methodNameResolver = new MethodNameResolver();

        return new RuleSet(
            new Fixers(
                new NameWrapper(
                    new LineBreakAfterStatementsFixer(),
                    'ErickSkrauch/line_break_after_statements',
                ),
                new NameWrapper(
                    new ArrayListItemNewlineFixer($arrayItemNewliner, $arrayAnalyzer, $arrayBlockInfoFinder),
                    'Symplify/array_list_item_newline_fixer',
                ),
                new NameWrapper(
                    new ArrayOpenerAndCloserNewlineFixer($arrayBlockInfoFinder, $whitespacesFixerConfig, $arrayAnalyzer),
                    'Symplify/array_opener_and_closer_newline_fixer',
                ),
                new NameWrapper(
                    new StandaloneLineInMultilineArrayFixer($arrayWrapperFactory, $tokensNewliner, $blockfinder),
                    'Symplify/standalone_line_in_multiline_array_fixer',
                ),
                new NameWrapper(
                    new StandaloneLineConstructorParamFixer($paramNewliner, $methodNameResolver),
                    'Symplify/standalone_line_constructor_param_fixer',
                ),
            ),
            'TicketSwap',
            new Rules([
                'ErickSkrauch/line_break_after_statements' => true,
                'Symplify/array_list_item_newline_fixer' => true,
                'Symplify/array_opener_and_closer_newline_fixer' => true,
                'Symplify/standalone_line_in_multiline_array_fixer' => true,
                'Symplify/standalone_line_constructor_param_fixer' => true,

                // Rule sets
                '@PER-CS2.0' => true,

                // Rules (keep sorted A-Z)
                'align_multiline_comment' => true,
                'array_indentation' => true,
                'array_syntax' => [
                    'syntax' => 'short',
                ],
                'attribute_empty_parentheses' => true,
                'backtick_to_shell_exec' => true,
                'binary_operator_spaces' => true,
                'blank_line_before_statement' => [
                    'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try', 'if'],
                ],
                'blank_line_between_import_groups' => false,
                'braces_position' => [
                    'allow_single_line_anonymous_functions' => true,
                    'allow_single_line_empty_anonymous_classes' => true,
                ],
                'cast_spaces' => true,
                'class_attributes_separation' => [
                    'elements' => [
                        'method' => 'one',
                        'property' => 'only_if_meta',
                    ],
                ],
                'class_definition' => [
                    'single_line' => true,
                ],
                'class_reference_name_casing' => true,
                'clean_namespace' => true,
                'compact_nullable_type_declaration' => true,
                'concat_space' => [
                    'spacing' => 'one',
                ],
                'declare_parentheses' => true,
                'declare_strict_types' => true,
                'echo_tag_syntax' => true,
                'empty_loop_body' => [
                    'style' => 'braces',
                ],
                'empty_loop_condition' => true,
                'fully_qualified_strict_types' => true,
                'general_phpdoc_tag_rename' => [
                    'replacements' => [
                        'inheritDocs' => 'inheritDoc',
                    ],
                ],
                'global_namespace_import' => [
                    'import_classes' => true,
                    'import_constants' => false,
                    'import_functions' => false,
                ],
                'heredoc_indentation' => [
                    'indentation' => 'start_plus_one',
                ],
                'include' => true,
                'increment_style' => true,
                'integer_literal_case' => true,
                'lambda_not_used_import' => true,
                'linebreak_after_opening_tag' => true,
                'magic_constant_casing' => true,
                'magic_method_casing' => true,
                'method_argument_space' => [
                    'on_multiline' => 'ensure_fully_multiline',
                    'attribute_placement' => 'standalone',
                ],
                'native_function_casing' => true,
                'native_type_declaration_casing' => true,
                'no_alias_language_construct_call' => true,
                'no_alternative_syntax' => true,
                'no_binary_string' => true,
                'no_blank_lines_after_phpdoc' => true,
                'no_empty_comment' => true,
                'no_empty_phpdoc' => true,
                'no_empty_statement' => true,
                'no_extra_blank_lines' => [
                    'tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use'],
                ],
                'no_leading_namespace_whitespace' => true,
                'no_mixed_echo_print' => true,
                'no_multiline_whitespace_around_double_arrow' => true,
                'no_null_property_initialization' => false,
                'no_short_bool_cast' => true,
                'no_singleline_whitespace_before_semicolons' => true,
                'no_spaces_around_offset' => true,
                'no_superfluous_phpdoc_tags' => [
                    'allow_mixed' => true,
                    'remove_inheritdoc' => true,
                ],
                'no_trailing_comma_in_singleline' => true,
                'no_unneeded_braces' => [
                    'namespaces' => true,
                ],
                'no_unneeded_control_parentheses' => [
                    'statements' => ['break', 'clone', 'continue', 'echo_print', 'others', 'return', 'switch_case', 'yield', 'yield_from'],
                ],
                'no_unneeded_import_alias' => true,
                'no_unset_cast' => true,
                'no_unused_imports' => true,
                'no_useless_concat_operator' => true,
                'no_useless_else' => true,
                'no_useless_nullsafe_operator' => true,
                'no_whitespace_before_comma_in_array' => true,
                'normalize_index_brace' => true,
                'not_operator_with_space' => true,
                'not_operator_with_successor_space' => true,
                'nullable_type_declaration' => [
                    'syntax' => 'question_mark',
                ],
                'nullable_type_declaration_for_default_null_value' => true,
                'object_operator_without_whitespace' => true,
                'operator_linebreak' => false,
                'ordered_attributes' => true,
                'ordered_imports' => true,
                'ordered_types' => [
                    'null_adjustment' => 'always_first',
                    'sort_algorithm' => 'alpha',
                ],
                'phpdoc_align' => [
                    'align' => 'left',
                ],
                'phpdoc_annotation_without_dot' => false,
                'phpdoc_indent' => true,
                'phpdoc_inline_tag_normalizer' => true,
                'phpdoc_line_span' => [
                    'const' => 'multi',
                    'method' => 'multi',
                    'property' => 'multi',
                ],
                'phpdoc_no_access' => true,
                'phpdoc_no_alias_tag' => false,
                'phpdoc_no_package' => true,
                'phpdoc_no_useless_inheritdoc' => true,
                'phpdoc_order' => true,
                'phpdoc_param_order' => true,
                'phpdoc_return_self_reference' => true,
                'phpdoc_scalar' => true,
                'phpdoc_separation' => false,
                'phpdoc_single_line_var_spacing' => true,
                'phpdoc_summary' => false,
                'phpdoc_tag_type' => [
                    'tags' => [
                        'inheritDoc' => 'inline',
                    ],
                ],
                'phpdoc_to_comment' => [
                    'ignored_tags' => ['return', 'var', 'see', 'deprecated', 'todo'],
                ],
                'phpdoc_trim' => true,
                'phpdoc_trim_consecutive_blank_line_separation' => true,
                'phpdoc_types' => true,
                'phpdoc_types_order' => [
                    'null_adjustment' => 'always_first',
                    'sort_algorithm' => 'none',
                ],
                'phpdoc_var_without_name' => true,
                'php_unit_test_case_static_method_calls' => [
                    'call_type' => 'self',
                ],
                'return_type_declaration' => [
                    'space_before' => 'one',
                ],
                'semicolon_after_instruction' => true,
                'simple_to_complex_string_variable' => true,
                'single_class_element_per_statement' => true,
                'single_import_per_statement' => true,
                'single_line_comment_spacing' => true,
                'single_line_comment_style' => true,
                'single_line_empty_body' => true,
                'single_line_throw' => false,
                'single_quote' => true,
                'single_space_around_construct' => true,
                'single_trait_insert_per_statement' => true,
                'space_after_semicolon' => [
                    'remove_in_empty_for_expressions' => true,
                ],
                'standardize_increment' => true,
                'standardize_not_equals' => true,
                'switch_continue_to_break' => true,
                'trailing_comma_in_multiline' => [
                    'elements' => ['arrays', 'arguments', 'match', 'parameters'],
                ],
                'trim_array_spaces' => true,
                'type_declaration_spaces' => true,
                'types_spaces' => [
                    'space' => 'single',
                ],
                'unary_operator_spaces' => true,
                'void_return' => true,
                'whitespace_after_comma_in_array' => true,
                'yoda_style' => [
                    'always_move_variable' => false,
                    'equal' => false,
                    'identical' => false,
                    'less_and_greater' => null,
                ],
            ]),
        );
    }
}
