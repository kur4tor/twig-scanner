<?php

require_once('vendor/autoload.php');

$dir = __DIR__ . '/templates';

$scanner = new \Kur4tor\TwigScanner\Scanner();
$scanner->scan($dir);