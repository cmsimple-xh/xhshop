<?php
$file = file_exists(XHS_HELP_PATH . XHS_LANGUAGE . '/about.html') ? XHS_HELP_PATH . XHS_LANGUAGE . '/about.html' : XHS_HELP_PATH . 'about.html';
include_once $file;
?>
<div class="xhs_syscheck">
    <h2><?php echo $this->label('syscheck')?></h2>
<?php foreach ($this->syschecks as $syscheck):?>
    <p class="xh_<?php echo $syscheck->state?>"><?php echo $this->syscheck($syscheck->label, $syscheck->stateLabel)?></p>
<?php endforeach?>
</div>
