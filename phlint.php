<?php

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
$phlint = new Phlint(new Interpreter(), new Parser(), new Lexer());
array_shift($argv);
$exitCode = $phlint->run($argv);
exit($exitCode);
