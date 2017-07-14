<?php

use Xhshop\BackEndController;

XH_registerStandardPluginMenuItems(false);

if (function_exists('XH_wantsPluginAdministration') && XH_wantsPluginAdministration('xhshop') || isset($xhshop)) {
    $plugin = basename(dirname(__FILE__), "/");
    $admin = isset($_GET['admin']) ? $_GET['admin'] : '';
    $admin .= isset($_POST['admin']) ? $_POST['admin'] : '';
    setcookie('xhsMode', 'edit');

    if ($admin == 'plugin_stylesheet') {
        $hint = array();
        $o .= plugin_admin_common($action, $admin, $plugin, $hint);
    } elseif (in_array($admin, array('plugin_config', 'plugin_language'), true)) {
        $o .= plugin_admin_common($action, $admin, $plugin);
    } else {
        $xhsController = new BackEndController();
        $o .= $xhsController->handleRequest();
    }
}
