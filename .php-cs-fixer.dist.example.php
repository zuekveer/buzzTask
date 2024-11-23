<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // Enables PSR-12 coding standard
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__ . '/src') // Path to your PHP source code
    );
