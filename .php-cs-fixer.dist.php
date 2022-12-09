<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('src/Migrations')
    ->exclude('var')
    ->exclude('vendor');

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    '@PHP80Migration:risky' => true,
    'php_unit_construct' => true,
    'php_unit_strict' => true,
])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
