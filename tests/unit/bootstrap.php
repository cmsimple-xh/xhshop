<?php

const XHS_BASE_PATH = __DIR__ . "/../../";

require_once '../../cmsimple/functions.php';

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    if ($parts[0] == 'Xhshop') {
        if (count($parts) === 3) {
            $parts[1] = lcfirst($parts[1]);
        }
        include_once __DIR__ . '/../../classes/' . implode('/', array_slice($parts, 1)) . '.php';
    }
});
