<?php

use Xhshop\BackEndController;

$temp = xhshop_pluginMenu();

if (function_exists('XH_wantsPluginAdministration') && XH_wantsPluginAdministration('xhshop') || isset($xhshop)) {
    setcookie('xhsMode', 'edit');

    $o .= print_plugin_admin('off');
    pluginmenu('ROW');
    foreach ($temp as $i => $j) {
        pluginmenu('TAB', XH_hsc($j), '', $i);
    }
    $o .= pluginmenu('SHOW');
    if (in_array($admin, array('plugin_stylesheet', 'plugin_config', 'plugin_language'), true)) {
        if (isset($_GET['xh_success']) && in_array($_GET['xh_success'], array('config', 'language'))) {
            header('Location: ' . CMSIMPLE_URL . '?xhshop&xhsTask=syscheck&normal');
            exit;
        }
        $o .= plugin_admin_common($action, $admin, $plugin);
    } else {
        $xhsController = new BackEndController();
        $o .= $xhsController->handleRequest();
    }
}

/** @return array */
function xhshop_pluginMenu()
{
    global $su, $plugin_cf, $plugin_tx;

    $baseurl = substr($plugin_tx['xhshop']['config_shop_page'], 1);
    $baseurl = $su === $baseurl ? $baseurl : 'xhshop';
    $items = array();
    $items[$plugin_tx['xhshop']['labels_products_list']] = "?{$baseurl}&xhsTask=productList&normal";
    $items[$plugin_tx['xhshop']['labels_new_product']] = "?{$baseurl}&xhsTask=editProduct&normal";
    if ($plugin_cf['xhshop']['categories_use_categories']) {
        $items[$plugin_tx['xhshop']['labels_product_categories']] = "?{$baseurl}&xhsTask=productCategories&normal";
    }
    foreach ($items as $label => $url) {
        XH_registerPluginMenuItem('xhshop', $label, $url);
    }
    XH_registerStandardPluginMenuItems(false);
    $label = $plugin_tx['xhshop']['labels_syscheck'];
    $url = "?{$baseurl}&xhsTask=syscheck&normal";
    XH_registerPluginMenuItem('xhshop', $label, $url);
    $items[$label] = $url;
    $label = $plugin_tx['xhshop']['labels_about'];
    $url = "?{$baseurl}&xhsTask=helpAbout&normal";
    XH_registerPluginMenuItem('xhshop', $label, $url);
    $items[$label] = $url;
    return $items;
}
