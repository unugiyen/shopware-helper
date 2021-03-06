<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder)
    ;
