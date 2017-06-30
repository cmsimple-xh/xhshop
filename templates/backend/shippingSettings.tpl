<?php ?>
<section class="xhsMain xhsClearB">
	<h1><?php $this->label('forwarding_expenses'); ?></h1>
	<div id="xhsShippingDetails">
		<form method="post" action="" name="shipping">
			<p><?php echo $this->label('charge_for_shipping'); ?></p>
			<p><?php $params['onclick'] = 'xhsEnableShipping();' ?>
			<?php echo $this->radioNameValueLabel('charge_for_shipping', 'true', null, $params); ?>&nbsp;<?php echo $this->label('yes'); ?>&nbsp; &nbsp;
			<?php $params['onclick'] = 'xhsDisableShipping();' ?>
			<?php echo $this->radioNameValueLabel('charge_for_shipping', 'false', null, $params); ?>&nbsp;<?php echo $this->label('no'); ?></p>
			<p><?php echo $this->checkboxNameValueLabel('shipping_up_to', 'true') ;?>
			<?php $this->label('forwarding_expenses_up_to'); ?>
			<?php echo $this->moneyinputNameValueLabel('forwarding_expenses_up_to',
				(float)$this->shipping_limit,
				// 123.45,
				null,
				$this->disabled); ?></p>
			<h2><?php echo $this->label($this->shipping_mode) ; ?></h2>
			<p class="xhsHint"><?php $this->hint('shipping_graded'); ?></p>
			<table class="xhsTable">
				<?php
				$previous = $this->formatFloat(0.00);
				if(isset($this->weightRanges)){?>
				<?php if(count($this->weightRanges) > 0){ ?>
				<?php } ?>
				<?php foreach($this->weightRanges as $range => $fee){?>
				<tr>
					<td>> <?php echo $previous . '  ';$this->label('up_to'); ?></td>
					<td><?php echo $this->floatinputNameValueLabel('weightRange[' . $range . ']', $range, null, $this->disabled) . '&nbsp;' . $this->shipping_unit ?>
					</td>
					<td class="xhsTdR">=> <?php echo $this->moneyinputNameValueLabel('weightFee[' . $range . ']', $fee, null, $this->disabled) ;?></td>
				</tr>
				<?php $previous = $this->formatFloat($range); } } ?>
				<tr>
					<td colspan=2">> <?php echo $previous . ' ' . $this->shipping_unit; ?></td>
					<td class="xhsTdR">=> <?php echo $this->moneyinputNameValueLabel('shipping_max', $this->shipping_max, null, $this->disabled); ?></td>
				</tr>
				<tr>
					<td colspan="3"><h3><?php echo $this->label('new_weightRange'); ?></h3></td>
				</tr>
				<tr>
					<td><?php echo $this->label('up_to'); ?></td>
					<td><?php echo $this->floatinputNameValueLabel('newWeightRange', null, null, $this->disabled). '&nbsp;' . $this->shipping_unit ?></td>
					<td class="xhsTdR">=> <?php echo $this->moneyinputNameValueLabel('newWeightFee', null, null, $this->disabled);?></td>
				</tr>
			</table>
			<p>&nbsp;</p>
			<p><?php echo $this->labels['shipping_unit']; ?>: <?php echo $this->textinputNameValueLabel(XHS_LANGUAGE.'[shipping_unit]', $this->shipping_unit, null, $this->disabled) ?></p>
		</div>
		<p>&nbsp;</p>
		<div class="xhsRght">
			<input type="hidden" name="xhsTaskCat" value="setting_tasks">
			<input type="hidden" name="xhsTask" value="updateSettings">
			<input type="hidden" name="xhsPage" value="shippingSettings">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
		</div>
	</form>
</section>