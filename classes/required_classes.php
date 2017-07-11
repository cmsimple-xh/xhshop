<?php

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    if ($parts[0] == 'Xhshop') {
        include_once __DIR__ . '/' . implode('/', array_slice($parts, 1)) . '.php';
    }
});
