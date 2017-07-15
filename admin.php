<?php

use Xhshop\BackEndController;

$temp = substr($plugin_tx['xhshop']['config_shop_page'], 1);
$temp = $su === $temp ? $temp : 'xhshop';
$temp = array(
    $plugin_tx['xhshop']['labels_products_list'] => "?{$temp}&xhsTask=productList",
    $plugin_tx['xhshop']['labels_new_product'] => "?{$temp}&xhsTask=editProduct",
    $plugin_tx['xhshop']['labels_product_categories'] => "?{$temp}&xhsTask=productCategories",
    $plugin_tx['xhshop']['labels_about'] => "?{$temp}&xhsTask=helpAbout"
);

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
