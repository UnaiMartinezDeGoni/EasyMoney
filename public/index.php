<?php
// public/index.php

// 1) Autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// 2) Bootstrap de Lumen
$app = require __DIR__ . '/../bootstrap/app.php';

// 3) Ejecuta la aplicación
$app->run();
