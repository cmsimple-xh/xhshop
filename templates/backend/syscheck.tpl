<div class="xhs_syscheck">
    <h1><?php echo $this->label('syscheck')?></h1>
<?php foreach ($this->syschecks as $syscheck):?>
    <p class="xh_<?php echo $syscheck->state?>"><?php echo $this->syscheck($syscheck->label, $syscheck->stateLabel)?></p>
<?php endforeach?>
</div>
