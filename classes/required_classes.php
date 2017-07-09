<?php
define('XHS_LANGUAGE', basename($sl));
define('XHS_BASE_PATH', $pth['folder']['plugins'] . 'xhshop/');
define('XHS_LIB_PATH', XHS_BASE_PATH . 'classes/app/' );
define('XHS_CATALOG', XHS_BASE_PATH . 'data/catalog.php' );
define('XHS_JS_PATH', XHS_BASE_PATH .'js/' );

define('XHS_COUNTRIES_FILE', XHS_BASE_PATH . 'lang/countries_' . XHS_LANGUAGE . '.txt');
define('XHS_CONFIG_FILE', XHS_BASE_PATH . 'config/shopsettings.php' );
define('XHS_BILLS_PATH', XHS_BASE_PATH . 'bills/');
define('XHS_TEMPLATES_PATH', XHS_BASE_PATH . 'templates/');
define('XHS_CSS_PATH', XHS_BASE_PATH .  'css/');
define('XHS_URI_SEPARATOR', $cf['uri']['seperator']);

loadXHSClasses();

function loadXHSClasses(){
    require_once XHS_LIB_PATH . 'xhs_view.php';
    require_once XHS_LIB_PATH . 'xhs_controller.php';
    require_once XHS_LIB_PATH . 'xhs_frontend_controller.php';
    require_once XHS_LIB_PATH . 'xhs_backend_controller.php';
    require_once XHS_LIB_PATH . 'xhs_frontend_view.php';
    require_once XHS_LIB_PATH . 'xhs_backend_view.php';
    require_once XHS_LIB_PATH . 'xhs_order.php';
    require_once XHS_LIB_PATH . 'xhs_customer.php';
    require_once XHS_LIB_PATH . 'catalogue.php';
    require_once XHS_LIB_PATH . 'product.php';
    require_once XHS_LIB_PATH . 'xhs_system_check_service.php';
    require_once XHS_LIB_PATH . 'xhs_payment_module.php';
}
