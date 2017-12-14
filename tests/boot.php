<?php
/**
 * phpunit
 * OR
 * phpunit --bootstrap tests/boot.php tests
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    $file = null;

    if (0 === strpos($class,'Inhere\LiteDb\Examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\LiteDb\Examples\\')));
        $file = dirname(__DIR__) . "/examples/{$path}.php";
    } elseif (0 === strpos($class,'Inhere\LiteDb\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\LiteDb\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});