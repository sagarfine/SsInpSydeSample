<?php
declare(strict_types=1);
$directory=dirname(__DIR__, 2);
if (!file_exists($directory. '/vendor/autoload.php')) {
    echo 'Autoload file not found. Please run `composer install`.';
    die(1);
}
require_once $directory. '/vendor/autoload.php';

// Include the class for PluginTestCase
require_once __DIR__ . '/inc/SsInpSydeTest.php';
