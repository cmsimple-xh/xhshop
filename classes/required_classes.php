<?php
define('XHS_LANGUAGE', basename($sl));
define('XHS_BASE_PATH', $pth['folder']['plugins'] . 'xhshop/');
define('XHS_CATALOG', XHS_BASE_PATH . 'data/catalog.php' );

define('XHS_COUNTRIES_FILE', XHS_BASE_PATH . 'lang/countries_' . XHS_LANGUAGE . '.txt');
define('XHS_BILLS_PATH', XHS_BASE_PATH . 'bills/');
define('XHS_TEMPLATES_PATH', XHS_BASE_PATH . 'templates/');
define('XHS_URI_SEPARATOR', $cf['uri']['seperator']);

spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    if ($parts[0] == 'Xhshop') {
        include_once __DIR__ . '/' . implode('/', array_slice($parts, 1)) . '.php';
    }
});
