<?php

use Xhshop\BackEndController;

XH_registerStandardPluginMenuItems(false);

if (function_exists('XH_wantsPluginAdministration') && XH_wantsPluginAdministration('xhshop') || isset($xhshop)) {
    setcookie('xhsMode', 'edit');

    if (in_array($admin, array('plugin_stylesheet', 'plugin_config', 'plugin_language'), true)) {
        $o .= plugin_admin_common($action, $admin, $plugin);
    } else {
        $xhsController = new BackEndController();
        $o .= $xhsController->handleRequest();
    }
}
