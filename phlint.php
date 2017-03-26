<?php

use Simbigo\Phlint\Configuration\IniConfiguration;
use Simbigo\Phlint\Environment;
use Simbigo\Phlint\Interpreter;
use Simbigo\Phlint\Lexer;
use Simbigo\Phlint\Parser;
use Simbigo\Phlint\Phlint;

spl_autoload_register(function ($className) {
    $classPath = str_replace('Simbigo\Phlint\\', __DIR__ . '/src/', $className);
    $classPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $classPath) . '.php';
    if (file_exists($classPath)) {
        /** @noinspection PhpIncludeInspection */
        require $classPath;
    }
});

array_shift($argv);

$config = new IniConfiguration();
$config->read('phlint.ini');
$phlint = new Phlint($config, new Interpreter(), new Parser(), new Lexer(), new Environment());
$exitCode = $phlint->run($argv);

exit($exitCode);
