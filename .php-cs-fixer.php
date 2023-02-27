<?php

$rules = [
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'multiline_whitespace_before_semicolons' => true,
    'echo_tag_syntax' => true,
    'no_unused_imports' => true,
    'phpdoc_var_without_name' => false,
];
$excludes = [
    'vendor',
];
$finder = PhpCsFixer\Finder::create()
    ->exclude($excludes)
    ->notName('README.md');

return (new PhpCsFixer\Config())->setRules($rules)->setFinder($finder);
