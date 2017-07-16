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
        $o .= plugin_admin_common($action, $admin, $plugin);
    } else {
        $xhsController = new BackEndController();
        $o .= $xhsController->handleRequest();
    }
}

function xhshop_pluginMenu()
{
    global $su, $plugin_cf, $plugin_tx;

    $url = substr($plugin_tx['xhshop']['config_shop_page'], 1);
    $url = $su === $url ? $url : 'xhshop';
    $items = array();
    $items[$plugin_tx['xhshop']['labels_products_list']] = "?{$url}&xhsTask=productList";
    $items[$plugin_tx['xhshop']['labels_new_product']] = "?{$url}&xhsTask=editProduct";
    if ($plugin_cf['xhshop']['categories_use_categories']) {
        $items[$plugin_tx['xhshop']['labels_product_categories']] = "?{$url}&xhsTask=productCategories";
    }
    foreach ($items as $label => $url) {
        XH_registerPluginMenuItem('xhshop', $label, $url);
    }
    XH_registerStandardPluginMenuItems(false);
    $label = $plugin_tx['xhshop']['labels_about'];
    $url = "?{$url}&xhsTask=helpAbout";
    XH_registerPluginMenuItem('xhshop', $label, $url);
    $items[$label] = $url;
    return $items;
}
