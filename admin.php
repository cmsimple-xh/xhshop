<?php

XH_registerStandardPluginMenuItems(false);

if(function_exists('XH_wantsPluginAdministration') && XH_wantsPluginAdministration('xhshop') || isset($xhshop)){
    $plugin = basename(dirname(__FILE__),"/");
    $admin = isset($_GET['admin']) ? $_GET['admin'] : '';
    $admin .= isset($_POST['admin']) ? $_POST['admin'] : '';
    setcookie('xhsMode', 'edit');

    if($admin == 'plugin_stylesheet') {
        $o .= '<p><a href="?&' . $plugin . '" />setting</a></p>';
        $hint = array();
        $o .= plugin_admin_common($action,$admin,$plugin, $hint);
    } elseif ($admin === 'plugin_language') {
        $o .= plugin_admin_common($action, $admin, $plugin);
    } else{
        $xhsController = new XHS_Backend_Controller();
        $o .= '<p><a href="?&' . $plugin . '&admin=plugin_stylesheet&action=plugin_text" />stylesheet</a></p>';
        $o .= $xhsController->handleRequest();
    }
}
?>