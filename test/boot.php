<?php
/**
 * phpunit
 * OR
 * phpunit --bootstrap tests/boot.php tests
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

$vendorDir = dirname(__DIR__, 3);

spl_autoload_register(function ($class) use($vendorDir) {
    $file = null;

    if (0 === strpos($class,'Inhere\LiteCache\Examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\LiteCache\Examples\\')));
        $file = dirname(__DIR__) . "/examples/{$path}.php";
    } elseif (0 === strpos($class,'Inhere\LiteCache\Tests\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\LiteCache\Tests\\')));
        $file = dirname(__DIR__) . "/{$path}.php";
    } elseif (0 === strpos($class,'Inhere\LiteCache\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\LiteCache\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    } elseif (0 === strpos($class,'Psr\SimpleCache\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Psr\SimpleCache\\')));
        $file = $vendorDir . "/psr/simple-cache/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});
