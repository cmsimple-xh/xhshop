<?php
require_once 'XHS_CMS_Bridge.php';

if (function_exists('XH_startSession')) {
	XH_startSession();
} elseif(session_id() == ''){
	session_start();
}

$xhsCartPreview = false;

if($adm == true){
	if(isset($_GET['xhsMode'])){
		setcookie('xhsMode', $_GET['xhsMode']);
	}
	if(isset($_GET['xhsMode'])){
		$xhsMode = $_GET['xhsMode'];
	} else {
		$xhsMode = isset($_COOKIE['xhsMode']) ? $_COOKIE['xhsMode'] : 'edit' ;
	}
    if($normal){$xhsMode = 'edit';}
    if($xhsMode !== 'preview'){
		$xhsController = new XHS_Backend_Controller();
		if(!$edit && $su !== $xhsController->settings['url']){
			$xhsController = new XHS_Frontend_Controller();
		}
	} else {$xhsController = new XHS_Frontend_Controller();}
} else {$xhsController = new XHS_Frontend_Controller();}

if($xhsController->settings['url'] == $su && $xhsController instanceof XHS_Backend_Controller){
	$hjs .= '<script src="'.$pth['folder']['plugins'].'/xhshop/js/xhsbackend.js"></script>';
}

if($xhsController instanceof XHS_Frontend_Controller
    && $xhsController->settings['published']
    && !$edit) {
    if(isset($_POST['xhsTask'])){
        if($_POST['xhsTask'] == 'updateCart'){
            $xhsController->updateCart();
        }
    }

// cmb    if(!isset($_POST['xhsCheckout']) && (int)$s > -1){
// disable cartPreview (Button) in print view (or fancybox)
	 if(!isset($_POST['xhsCheckout']) && (int)$s > -1 && !$print){
        $xhsCartPreview = $xhsController->cartPreview();

        if(!strpos(file_get_contents($pth['file']['template']), '$xhsCartPreview')){
            $c[$s] = $xhsController->cartPreview() . $c[$s];
        }
    }

    $xhsCartButtons = '';
    $products = $xhsController->getPagesProducts();
  
    if(count($products) > 0){
        foreach($products as $product){
            $xhsCartButtons .= $xhsController->addToCartButton($product);
        }
        
        if($s >= 0){
             $c[$s] .= $xhsCartButtons;
            //$c[$s] =  preg_replace('/(<\/h[1-'.$cf['menu']['levels'].']>)/i', "$1 $xhsCartButtons", $c[$s], 1);
        }
    }
}

if ($f === 'xh_loggedout') {
	$temp = XHS_BASE_PATH . 'data/catalog.bak.php';
    if (copy(XHS_CATALOG, $temp)) {
		$o .= XH_message('info', sprintf($plugin_tx['xhshop']['message_backup_created'], $temp));
	}
}

function display_shop() {
	global $xhsController, $adm, $su, $sn;
	$html = '';
	if($adm){
		if($xhsController instanceof XHS_Frontend_Controller){
			$html .= '<a href="'.$sn.'?'.$su.'&xhsMode=edit" class="xhsToggleBtn"><span class="fa fa-edit fa-fw fa-lg"></span> Shop edit</a>';
		} else {
			$html .= '<a href="'.$sn.'?'.$su.'&xhsMode=preview" class="xhsToggleBtn"><span class="fa fa-eye fa-fw fa-lg"></span> Shop preview</a>';
		}
	}
	return $html . $xhsController->handleRequest();
}
