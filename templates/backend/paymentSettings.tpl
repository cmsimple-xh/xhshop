<?php ?>
<section class="xhsMain xhsClearB">
	<h1><?php $this->label('payments'); ?></h1>
	<p><?php $this->hint('payments'); ?></p>
        <p>
	<form method="post" action="">
	<?php foreach($this->modules as $module){
		if($module->isAvailable()){
			$module->isActive() ? $checked = 'checked="checked"' : $checked = ''; ?>
		<p><input type="checkbox"<?php echo $checked;?> name="<?php echo $module->getName();?>_checked"> 
		<strong><?php echo $module->getLabelString(); ?></strong><br>
		<?php echo $this->label('fee'); ?>/<?php echo $this->label('reduction'); ?>
		<?php echo $this->moneyinputNameValueLabel($module->getName() .'_fee', $module->getFee()); ?></p>
	<?php } } ?>
		<div class="xhsRght">
			<input type="hidden" name="xhsPaymentTask" value="updateSettings">
			<input type="hidden" name="xhsTaskCat" value="setting_tasks">
			<input type="hidden" name="xhsTask" value="paymentSettings">
			<input type="hidden" name="xhsPage" value="paymentSettings">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
        </div>
	</form>
	<p>&nbsp;</p>
	<hr>
	<p>&nbsp;</p>
	<?php
	foreach($this->modules as $module){
		if($module->needsConfig()){ ?>
	<form method="post" action="">
	<p><?php echo $module->getLabel(); ?></p>
	<?php echo $module->settingInputs(); ?>
	<div class="xhsRght">
		<input type="hidden" name="xhsEPayment" value="<?php echo $module->getName(); ?>">
		<input type="hidden" name="xhsTaskCat" value="setting_tasks">
		<input type="hidden" name="xhsTask" value="paymentSettings">
		<input type="hidden" name="xhsPage" value="paymentSettings">
		<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
	</div>
	</form>
	<?php } } ?>
</section>