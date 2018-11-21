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

    if (0 === strpos($class,'PhpComp\LiteCache\Examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('PhpComp\LiteCache\Examples\\')));
        $file = dirname(__DIR__) . "/examples/{$path}.php";
    } elseif (0 === strpos($class,'PhpComp\LiteCache\Tests\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('PhpComp\LiteCache\Tests\\')));
        $file = dirname(__DIR__) . "/{$path}.php";
    } elseif (0 === strpos($class,'PhpComp\LiteCache\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('PhpComp\LiteCache\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    } elseif (0 === strpos($class,'Psr\SimpleCache\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Psr\SimpleCache\\')));
        $file = $vendorDir . "/psr/simple-cache/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});
