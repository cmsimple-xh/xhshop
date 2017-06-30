<?php ?>
<section class="xhsMain xhsClearB">
	<h1><?php $this->label('contact_settings'); ?></h1>
	<form method="post" action="">
		<p><?php echo $this->labels['order_email']; ?>:<br>
			<input type="text" name="order_email" value="%EMAIL%" size="30">
		</p>
		<p><?php echo $this->labels['company_name']; ?>:<br>
			<input type="text" name="company_name" value="%COMPANY_NAME%" size="30">
		</p>
		<p><?php echo $this->labels['your_name']; ?>:<br>
			<input type="text" name="name" value="%NAME%" size="30">
		</p>
		<p><?php echo $this->labels['street']; ?>:<br>
			<input type="text" name="street" value="%STREET%" size="30">
		</p>
		<p><?php echo $this->labels['zip_code']; ?>:<br>
			<input type="text" name="zip_code" value="%ZIP_CODE%" size="30">
		</p>
		<p><?php echo $this->labels['city']; ?>:<br>
			<input type="text" name="city" value="%CITY%" size="30">
		</p>
		<div class="xhsRght">
			<input type="hidden" name="xhsTask" value="updateSettings">
			<input type="hidden" name="xhsPage" value="contactSettings">
			<input type="hidden" name="xhsTaskCat" value="setting_tasks">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
        </div>
	</form>
</section>
