<?php ?>
<section class="xhsMain xhsClearB">
	<h1><?php $this->label('vat_settings'); ?></h1>
	<form method="post" action="" name="financial">
		<p><?php echo $this->labels['vat_rates']; ?></p>
		<p><?php echo $this->floatinputNameValueLabel('vat_full', $this->vat_full)?> % = <?php echo $this->labels['full_vat']?></p> 
		<p><?php echo $this->floatinputNameValueLabel('vat_reduced', $this->vat_reduced, 'reduced_vat')?> % = <?php echo $this->labels['reduced_vat']?></p>
		<p>&nbsp;</p>
		<h2><?php echo $this->labels['vat_default'] ?>:</h2>
		<p><?php echo $this->radioNameValueLabel('vat_default', 'full');?>&nbsp;<?php echo $this->label('full_vat'); ?>&nbsp; &nbsp;<?php echo $this->radioNameValueLabel('vat_default', 'reduced');?>&nbsp;<?php echo $this->label('reduced_vat'); ?></p>
		<p>
		<?php
		//var_dump($_POST['dont_deal_with_taxes']);
		//var_dump($this->dont_deal_with_taxes);
		echo $this->checkboxNameValueLabel('dont_deal_with_taxes', 'true');?> <?php echo $this->label('dont_deal_with_taxes'); ?>
		</p>
		<p>&nbsp;</p>
		<div class="xhsRght">
			<input type="hidden" name="xhsTaskCat" value="setting_tasks">
			<input type="hidden" name="xhsTask" value="updateSettings">
			<input type="hidden" name="xhsPage" value="taxSettings">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
        </div>
	</form>
</section>