<?php

namespace Kur4tor;

use Kur4tor\TwigScanner\Scanner;

require_once(__DIR__ . '/../vendor/autoload.php');

$dir = __DIR__ . '/../templates';

$scanner = new Scanner();
$scanner->scan($dir);