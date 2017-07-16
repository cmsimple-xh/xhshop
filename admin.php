<?php

use Xhshop\BackEndController;

$i = substr($plugin_tx['xhshop']['config_shop_page'], 1);
$i = $su === $i ? $i : 'xhshop';
$temp = array();
$temp[$plugin_tx['xhshop']['labels_products_list']] = "?{$i}&xhsTask=productList";
$temp[$plugin_tx['xhshop']['labels_new_product']] = "?{$i}&xhsTask=editProduct";
if ($plugin_cf['xhshop']['categories_use_categories']) {
    $temp[$plugin_tx['xhshop']['labels_product_categories']] = "?{$i}&xhsTask=productCategories";
}
$temp[$plugin_tx['xhshop']['labels_about']] = "?{$i}&xhsTask=helpAbout";

foreach ($temp as $i => $j) {
    XH_registerPluginMenuItem('xhshop', $i, $j);
}
XH_registerStandardPluginMenuItems(false);

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
