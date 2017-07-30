<?php

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    if ($parts[0] == 'Xhshop') {
        if (count($parts) === 3) {
            $parts[1] = lcfirst($parts[1]);
        }
        include_once __DIR__ . '/' . implode('/', array_slice($parts, 1)) . '.php';
    }
});
