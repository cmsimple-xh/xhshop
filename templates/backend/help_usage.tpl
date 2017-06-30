<?php
$file = file_exists(XHS_HELP_PATH . XHS_LANGUAGE  . '/usage.html') ? XHS_HELP_PATH . XHS_LANGUAGE . '/usage.html' : XHS_HELP_PATH . 'usage.html';
include_once $file;
?>