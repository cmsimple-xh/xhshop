<?php

use Xhshop\BackEndController;
use Xhshop\FrontEndController;

define('XHS_LANGUAGE', basename($sl));
define('XHS_BASE_PATH', $pth['folder']['plugins'] . 'xhshop/');
define(
    'XHS_CONTENT_PATH',
    preg_replace('/(?:\/[^\/]+\/\.\.\/|\/\.\/)$/', '/', "{$pth['folder']['content']}{$pth['folder']['base']}")
);
define('XHS_CATALOG', XHS_CONTENT_PATH . 'xhshop/catalog.php');

define('XHS_TEMPLATES_PATH', XHS_BASE_PATH . 'templates/');
define('XHS_URI_SEPARATOR', $cf['uri']['seperator']);

if (function_exists('XH_startSession')) {
    XH_startSession();
} elseif (session_id() == '') {
    session_start();
}

if (class_exists('Fa\RequireCommand')) {
    $command = new Fa\RequireCommand;
    $command->execute();
}

$xhsCartPreview = false;

if (defined('XH_ADM') && XH_ADM) {
    if (isset($_GET['xhsMode'])) {
        setcookie('xhsMode', $_GET['xhsMode']);
    }
    if (isset($_GET['xhsMode'])) {
        $xhsMode = $_GET['xhsMode'];
    } else {
        $xhsMode = isset($_COOKIE['xhsMode']) ? $_COOKIE['xhsMode'] : 'edit' ;
    }
    if ($normal) {
        $xhsMode = 'edit';
    }
    if ($xhsMode !== 'preview') {
        $xhsController = new BackendController();
        if (!$edit && $su !== $xhsController->settings['url']) {
            $xhsController = new FrontEndController();
        }
    } else {
        $xhsController = new FrontEndController();
    }
    if ($xhsController->hasSystemCheckFailure()) {
        $o .= '<p class="xh_fail">' . $plugin_tx['xhshop']['hints_errors'] . '</p>';
    }
} else {
    $xhsController = new FrontEndController();
}

$hjs .= '<script src="'.$pth['folder']['plugins'].'xhshop/js/xhs.min.js"></script>';

if ($xhsController instanceof FrontEndController
    && $xhsController->settings['published']
    && !$edit) {
    if (isset($_POST['xhsTask'])) {
        if ($_POST['xhsTask'] == 'updateCart') {
            if (empty($_COOKIE)) {
                $o .= XH_message('info', $plugin_tx['xhshop']['message_no_cookie']);
            } else {
                $xhsController->updateCart();
            }
        }
    }

    // disable cartPreview (Button) also in print view (or fancybox)
    if (!isset($_GET['xhsCheckout']) && (int)$s > -1 && !$print) {
        $xhsCartPreview = $xhsController->cartPreview();

        if (!strpos(XH_readFile($pth['file']['template']), '$xhsCartPreview')) {
            $c[$s] = $xhsController->cartPreview() . $c[$s];
        }
    }

    $xhsCartButtons = '';
    $products = $xhsController->getPagesProducts();

    if (count($products) > 0) {
        foreach ($products as $product) {
            $xhsCartButtons .= $xhsController->addToCartButton($product);
        }
        
        if ($s >= 0) {
             $c[$s] .= $xhsCartButtons;
            //$c[$s] =  preg_replace('/(<\/h[1-'.$cf['menu']['levels'].']>)/i', "$1 $xhsCartButtons", $c[$s], 1);
        }
    }
}

if ($f === 'xh_loggedout') {
    $temp = XHS_CONTENT_PATH . 'xhshop/catalog.bak.php';
    if (copy(XHS_CATALOG, $temp)) {
        $o .= XH_message('info', sprintf($plugin_tx['xhshop']['message_backup_created'], $temp));
    }
}

function display_shop()
{
    global $xhsController, $su, $sn, $plugin_tx;
    $html = '';
    if (defined('XH_ADM') && XH_ADM) {
        if ($xhsController instanceof FrontEndController) {
            $html .= '<a href="' . $sn . '?' . $su
                . '&xhsMode=edit" class="xhsToggleBtn"><span class="fa fa-edit fa-fw fa-lg"></span> '
                . $plugin_tx['xhshop']['labels_edit'] . '</a>';
        } else {
            $html .= '<a href="' . $sn . '?' . $su
            . '&xhsMode=preview" class="xhsToggleBtn"><span class="fa fa-eye fa-fw fa-lg"></span> '
            . $plugin_tx['xhshop']['labels_preview'] . '</a>';
        }
    }
    return $html . $xhsController->handleRequest();
}
