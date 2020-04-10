<?php

return PhpCsFixer\Config::create()
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'linebreak_after_opening_tag' => true,
        'single_quote' => true,
        'ordered_imports' => false,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false,
        'single_trait_insert_per_statement' => false,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_no_empty_return' => true,
        'phpdoc_types' => true,
        'no_empty_phpdoc' => true,
        'class_definition' => [
            'multi_line_extends_each_single_line' => true,
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__.'/tests',
                __DIR__.'/src',
            ])
    )
;
