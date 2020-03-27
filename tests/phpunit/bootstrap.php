<?php
$autoload = realpath(__DIR__ . '/../../vendor/autoload.php');
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    echo 'Autoload file not found. Please run `composer install`.';
    die(1);
}

// Include the class for PluginTestCase
require_once __DIR__ . '/inc/SsInpSydeTest.php';
