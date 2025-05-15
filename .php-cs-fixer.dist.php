<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src'); // Ajusta la ruta según la estructura de tu proyecto

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PSR12' => true,
        // Agrega reglas adicionales aquí si las necesitas
    ])
    ->setFinder($finder);
