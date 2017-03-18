<?php

use Simbigo\Phlint\Exceptions\ParseException;
use Simbigo\Phlint\Interpreter;
use Simbigo\Phlint\Lexer;

spl_autoload_register(function ($className) {
    $classPath = str_replace('Simbigo\Phlint\\', __DIR__ . '/src/', $className);
    $classPath = str_replace('/', DIRECTORY_SEPARATOR, $classPath) . '.php';
    if (file_exists($classPath)) {
        /** @noinspection PhpIncludeInspection */
        require $classPath;
    }
});

mb_internal_encoding('UTF-8');
$exit = false;
$lexer = new Lexer();
$interpreter = new Interpreter($lexer);

if ($argc > 1) {
    $exit = true;
    $file = $argv[1];
    $code = file_get_contents($file);
    try {
        echo $interpreter->evaluate($code);
    } catch (ParseException $e) {
        echo $e->getMessage() . PHP_EOL;
        exit($e->getCode());
    }
    exit(0);
}

while (!$exit) {
    echo '[phlint]: ';

    $code = trim(fgets(STDIN));
    if ($code === 'exit' || $code === 'quit') {
        $exit = true;
    } else {
        try {
            echo $interpreter->evaluate($code) . PHP_EOL;
        } catch (ParseException $e) {
            echo $e->getMessage() . PHP_EOL;
            exit($e->getCode());
        }
    }
}

exit(0);