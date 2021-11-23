<?php $xhsCnt = 0; ?>
<section class="xhsMain">
<h1><?php $this->label('checkout_overview'); ?></h1>
<div id="xhsSteps">
<div class="xhsStep">1. <?php $this->label('checkout_step1'); ?></div>
<div class="xhsStep">2. <?php $this->label('checkout_step2'); ?></div>
<div class="xhsStep xhsNow">3. <?php $this->label('checkout_step3'); ?></div>
</div>
<div class="xhsClearB"></div>
<div class="xhsStepHint"><span class="fa fa-question-circle fa-fw"></span> <?php $this->hint('final_confirmation'); ?></div>
<h2><?php $this->label('summary'); ?></h2>
<dl class="xhsDl">
<dt><?php echo $this->label('delivery_adress'); ?>:</dt>
<dd>%FIRST_NAME% %LAST_NAME%</dd>
<dd>%STREET%</dd>
<dd>%EXTRA_ADDRESS_LINE%</dd>
<dd>%ZIP_CODE% %CITY%</dd>
<dd>%COUNTRY%</dd>
<dd>%EMAIL%</dd>
<dd>%PHONE%</dd>
<dt><?php $this->label('payment_mode') ?></dt>
<dd><?php echo $this->payment->getLabelString(); ?></dd>
<dt><?php $this->label('annotation') ?></dt>
<dd>%ANNOTATION%</dd>
</dl>
<p> </p>
<h3><?php echo $this->labels['cart']; ?></h3>
<table class="xhsCartTable">
	<tr>
		<td colspan="3"><hr class="xhsHr1"></td>
	</tr>
<?php foreach($this->cartItems as $product){
	$rowCounter = $product['itemCounter'];
	$xhsCnt++;?>
	<tr>
		<td class="xhsCnt"><?php echo $xhsCnt; ?>.</td>
		<td colspan="2"><h3><?php echo $product['name'];?> <?php echo $product['variantName']; ?></h3></td>
	</tr>
	<tr>
		<td class="xhsTdR" colspan="2"><?php echo $product['amount']; ?> x <?php echo $this->formatCurrency($product['price']); ?>
<?php if($this->hideVat === false):?>
		<span class="xhsVatInf">[<?php echo $this->formatPercentage($product['vatRate']); ?>]</span>
<?php endif?>
		</td>
		<td class="xhsMoneyCell"><strong><?php echo $this->formatCurrency($product['sum']); ?></strong></td>
	</tr>
	<tr>
		<td colspan="3"><hr class="xhsHr1"></td>
	</tr>
<?php } ?>
	<tr>
		<td class="xhsTdR" colspan="2"><?php $this->label('subtotal'); ?></td>
		<td class="xhsMoneyCell"><?php echo $this->formatCurrency($this->cartSum); ?></td>
	</tr>
	<tr>
		<td class="xhsTdR" colspan="2"><?php $this->label('forwarding_expenses'); ?></td>
		<td class="xhsMoneyCell"><?php echo $this->formatCurrency($this->shipping); ?></td>
	</tr>
<?php
if ($this->fee->isLessThan(Xhshop\Decimal::zero())){
	$feeLabel = $this->labels['reduction'];
} else {
	$feeLabel = $this->labels['fee'];
} ?>
	<tr>
		<td class="xhsTdR" colspan="2"><?php echo $feeLabel; ?></td>
		<td class="xhsMoneyCell"><?php echo $this->formatCurrency($this->fee); ?></td>
	</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td><hr class="xhsHr1"></td>
</tr>
	<tr class="xhsSum">
		<td class="xhsTdR" colspan="2"><?php $this->label('total'); ?></td>
		<td class="xhsMoneyCell"><strong><?php echo $this->formatCurrency($this->total); ?></strong></td>
	</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td><hr class="xhsHr2"></td>
</tr>
</table>
<?php if($this->hideVat === false){ ?>
<p class="xhsHint">
<?php echo $this->label('included_vat') . ' ' . $this->formatCurrency($this->vatTotal); ?>
 (<?php echo $this->formatPercentage($this->reducedRate); ?>&nbsp;= <?php echo $this->formatCurrency($this->vatReduced); ?>
 &ndash; <?php echo $this->formatPercentage($this->fullRate); ?>&nbsp;= <?php echo $this->formatCurrency($this->vatFull); ?>)</p>
<?php } else { ?>
<p class="xhsHint">
<?php echo $this->hint('price_info_no_vat')?>
</p>
<?php } ?>
<p> </p>
<div>
<div class="xhsLft">	
<form method="get">
<?php if (strlen(XHS_URL) > 0): ?>
<input type="hidden" name="selected" value="%XHS_URL%">
<?php endif; ?>
<input type="hidden" name="xhsCheckout" value="customersData">
<button class="xhsShopButton"><span class="fa fa-arrow-circle-left fa-fw"></span> <?php $this->label('back'); ?></button>
</form>
</div>
<div class="xhsRght">
<?php if($this->payment->orderSubmitForm() === false){ ?>
<form action="%XHS_CHECKOUT_URL%" method="post">
%CSRF_TOKEN_INPUT%
<button class="xhsShopButton xhsRght"><?php $this->label('send_order'); ?></button>
</form>
<?php
} else {
	echo $this->payment->orderSubmitForm();
} ?>
</div>
</div>
<p>&nbsp;</p>
</section>
<p>&nbsp;</p>

