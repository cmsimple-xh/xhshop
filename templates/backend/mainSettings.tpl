<?php ?>
<section class="xhsMain xhsClearB">
	<h1>Shop-<?php $this->label('settings'); ?></h1>
	<form method="post" action="">
		<p><strong><?php $this->label('shop_status'); ?></strong></p>
		<p><?php echo $this->radioNameValueLabel('published', 'true');?> <?php echo $this->label('is_published'); ?></p>
		<p><?php echo $this->radioNameValueLabel('published', 'false');?> <?php $this->label('is_hidden'); ?></p>
		<p><strong><?php $this->label($this->cos_label); ?>:</strong></p>
		<select name="<?php echo XHS_LANGUAGE;?>[cos_page]">
			<option value="">not linked</option>
			<?php  foreach($this->pages as $url => $heading){ ?>
			<option value="<?php echo $url; ?>"<?php if($url == $this->cos_page){echo ' selected="selected"';} ?>> <?php echo $heading; ?></option>
			<?php }?>
		</select>
		<p>&nbsp;</p>
		<p><strong><?php echo $this->labels['order_email']; ?>:</strong></p>
		<input type="text" name="order_email" value="%EMAIL%">
		<p>&nbsp;</p>
		<p><strong><?php echo $this->label('minimum_order'); ?>: </strong><br>
		<?php echo $this->hint('minimum_order'); ?></p>
		<?php echo $this->moneyInputNameValueLabel('minimum_order', $this->minimum_order) ?>
		<p>&nbsp;</p>
		<p><strong><?php $this->label('currency'); ?>:</strong></p>
		<label><?php echo $this->radioNameValueLabel('default_currency', '€');?> € </label> |
		<label><?php echo $this->radioNameValueLabel('default_currency', '$');?> $ </label> |
		<label><?php echo $this->radioNameValueLabel('default_currency', '£');?> £ </label> |
		<label><?php echo $this->radioNameValueLabel('default_currency', '¥');?> ¥ </label> |
		<label><?php echo $this->radioNameValueLabel('default_currency', 'other');?> <?php $this->label('other_currency'); ?></label>
		<input size="3" name="other_currency" value="%OTHER_CURRENCY%" type="text" >
		<p>&nbsp;</p>
		<div class="xhsRght">
			<input type="hidden" name="xhsTaskCat" value="setting_tasks">
			<input type="hidden" name="xhsTask" value="updateSettings">
			<input type="hidden" name="xhsPage" value="mainSettings">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
		</div>
	</form>
</section>