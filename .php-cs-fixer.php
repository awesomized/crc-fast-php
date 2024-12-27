<?php

/**
 * Configures PHP-CS-Fixer.
 */

declare(strict_types=1);

use PhpCsFixer\ParallelAwareConfigInterface;

$directories = [
    '.github/',
    'cli/',
    'src/',
    'tests/',
];

/** @var array<string, array<string, mixed|bool>> $rules */
$rules = [
    // we're on PHP-8.3
    '@PHP84Migration' => true,
    // use PER-CS https://www.php-fig.org/per/coding-style/
    '@PER-CS' => true,
    'class_attributes_separation' => [
        'elements' => [
            'method' => 'one',
            'property' => 'one',
        ],
    ],
    // ensure the correct casing is used for class references
    'class_reference_name_casing' => true,
    // combine consecutive issets and unsets into single calls
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    // always use strict types
    'declare_strict_types' => true,
    // add curly braces to indirect variables to make them clear to understand
    'explicit_indirect_variable' => true,
    // add curly braces to make complex string variables clear to understand
    'explicit_string_variable' => true,
    'method_chaining_indentation' => true,
    // use str_contains() and str_starts_with() instead of strpos()
    'modernize_strpos' => true,
    // use compiler-optimized functions
    'native_function_invocation' => true,
    // remove empty PHPDoc blocks
    'no_empty_phpdoc' => true,
    // remove superfluous PHPDoc tags, except for @param mixed
    'no_superfluous_phpdoc_tags' => [
        'allow_mixed' => true,
    ],
    // remove unused imports
    'no_unused_imports' => true,
    // sort implements/extends for interfaces alphabetically
    'ordered_interfaces' => true,
    // sort 'use' imports alphabetically
    'ordered_imports' => [
        'imports_order' => ['class', 'function', 'const'],
        'sort_algorithm' => 'alpha',
    ],
    // add untyped @param if missing
    'phpdoc_add_missing_param_annotation' => [
        'only_untyped' => true,
    ],
    // ensure PHPDoc is indented properly
    'phpdoc_indent' => true,
    // remove useless @inheritDoc tags
    'phpdoc_no_useless_inheritdoc' => true,
    // remove @package tags
    'phpdoc_no_package' => true,
    // remove empty @return tags
    'phpdoc_no_empty_return' => true,
    // order the PHPDoc elements, including our custom ones like @note
    'phpdoc_order' => [
        'order' => [
            'note',
            'todo',
            'deprecated',
            'see',
            'link',
            'param',
            'return',
            'throws',
        ],
    ],
    // use consistent scalar type names (bool instead of boolean, etc)
    'phpdoc_scalar' => true,
    // separate PHPDoc element types with a single blank line
    'phpdoc_separation' => true,
    // adds @return tag types to function signatures that are missing them
    'phpdoc_to_return_type' => true,
    // adds types to properties that are missing them
    'phpdoc_to_property_type' => true,
    // adds @param tag types to function signatures that are missing them
    'phpdoc_to_param_type' => true,
    // remove starting and ending newlines in PHPDocs
    'phpdoc_trim' => true,
    // orders PHPDoc types consistently, with nulls always last
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'none',
    ],
    // ensures that PHPDoc variable annotations have the correct order
    'phpdoc_var_annotation_correct_order' => true,
    // PHPUnit classes should be internal
    'php_unit_internal_class' => true,
    // use camelCase for PHPUnit test methods
    'php_unit_method_casing' => true,
    // use private properties when possible
    'protected_to_private' => true,
    // enforce PSR autoloading filenames and paths
    'psr_autoloading' => true,
    // use self:: instead of static:: in final classes
    'self_static_accessor' => true,
    // require constants and properties to each have their own statement
    'single_class_element_per_statement' => true,
    // use single quotes for simple strings
    'single_quote' => true,
    // use nullable type declarations for parameters with default null value
    'nullable_type_declaration_for_default_null_value' => true,
    // add void return types for functions missing them and @return tags
    'void_return' => true,
    // use yoda conditionals (e.g. if (true === $foo) rather than ($foo === true))
    'yoda_style' => true,
];

$finder = PhpCsFixer\Finder::create()
    ->in(
        dirs: $directories
    );

$config = (new PhpCsFixer\Config())
    ->setRules(
        rules: $rules
    )
    ->setRiskyAllowed(
        isRiskyAllowed: true
    )
    ->setFinder(
        finder: $finder
    );

/** @var ParallelAwareConfigInterface $config */

return $config->setParallelConfig(
    config: PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect()
);

