<section class="xhsMain">
<h1><?php $this->label('checkout_overview'); ?></h1>
<div id="xhsSteps">
<div class="xhsStep xhsNow">1. <?php $this->label('checkout_step1'); ?></div>
<div class="xhsStep">2. <?php $this->label('checkout_step2'); ?></div>
<div class="xhsStep">3. <?php $this->label('checkout_step3'); ?></div>
</div>
<div class="xhsClearB"></div>
<div class="xhsStepHint"><span class="fa fa-question-circle fa-fw"></span> <?php $this->hint('edit_cart'); ?></div>
<h2><?php echo $this->labels['cart']; ?></h2>
<table class="xhsCartTable">
<tr>
<td colspan="3"><hr class="xhsHr1"></td>
</tr>
<?php
$xhsCnt = 0;
foreach($this->cartItems as $product){
	$xhsCnt++; ?>
<tr>
<td class="xhsCnt"><p><?php echo $xhsCnt; ?>.</p></td>
<td colspan="2"><h3 class="xhsProdName"><?php echo $product['name']?><?php echo$product['variantName']; ?></h3>
<span class="xhsProdDescription"><?php echo $product['description']; ?></span></td>
</tr>
<tr>
<td>&nbsp;</td>
<td class="xhsMoneyCell">
<form action="%XHS_CHECKOUT_URL%" method="post" class="xhsInl">
%CSRF_TOKEN_INPUT%
<!--<input class="xhsInpAmount" type="number" min="1" step="1" name="xhsAmount" value="<?php echo $product['amount']; ?>" onChange="this.parentNode.submit()">-->
<input class="xhsInpAmount" type="number" min="1" step="1" name="xhsAmount" value="<?php echo $product['amount']; ?>"> x <?php echo $this->formatCurrency($product['price']) ;?><br>
<input type="hidden" name="xhsTask" value="updateCart">
<input type="hidden" name="xhsVariant" value="<?php echo $product['variantKey']; ?>">
<input type="hidden" name="cartItem" value="<?php echo $product['key'];?>">
<button class="xhsUpdBtn" title="<?php $this->label('update'); ?>"><span class="fa fa-refresh fa-lg"></span></button>
</form> <form action="%XHS_CHECKOUT_URL%" method="post" class="xhsInl">
%CSRF_TOKEN_INPUT%
<input type="hidden" name="xhsAmount" value="0">
<input type="hidden" name="xhsTask" value="updateCart">
<input type="hidden" name="xhsVariant" value="<?php echo $product['variantKey']; ?>">
<input type="hidden" name="cartItem" value="<?php echo $product['key'];?>">
<button class="xhsDelBtn" title="<?php $this->label('delete'); ?>"><span class="fa fa-remove fa-lg"></span></button>
</form></td>
<td class="xhsMoneyCell"><b><?php echo $this->formatCurrency($product['sum']); ?></b></td>
</tr>
<tr>
<td colspan="3"><hr class="xhsHr1"></td>
</tr>
<?php } ?>
<tr>
<td>&nbsp;</td>
<td class="xhsTdR"><?php echo $this->label('subtotal') ?></td>
<td class="xhsMoneyCell"><?php echo $this->formatCurrency($this->cartSum); ?></td>
</tr>
<tr>
<td>&nbsp;</td>
<td class="xhsTdR"><?php echo $this->label('forwarding_expenses') ?> (<?php  echo $this->formatFloat($this->units) . ' '. $this->unitName ; ?>)</td>
<td class="xhsMoneyCell"><?php echo $this->formatCurrency($this->shipping); ?></td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td><hr class="xhsHr1"></td>
</tr>
<tr class="xhsSum">
<td>&nbsp;</td>
<td class="xhsTdR"><b><?php $this->label('total') ?></b></td>
<td class="xhsMoneyCell"><b><?php echo $this->formatCurrency($this->total); ?></b></td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td><hr class="xhsHr2"></td>
</tr>
<tr>
<?php if( $this->shipping > 0 && $this->shipping_limit == 'true' && $this->canOrder !== false) { ?>
<td colspan="3" class="xhsHint">
<p><b><?php $this->label('hint'); ?>:</b> <?php $this->hint('no_shipping_from'); echo ' ' . $this->formatCurrency($this->no_shipping_from);?>.</p>
</td>
</tr>
<?php } ?>
<?php if (!$this->canOrder) { ?>
<td colspan="3" class="xhsHint">
<p><?php $this->hint('order_minimum_warn_1'); ?><?php echo $this->formatCurrency($this->minimum_order); ?><?php $this->hint('order_minimum_warn_2'); ?></p>
</td>
</tr>
<?php } ?>
<tr>
<td colspan="3">&nbsp;</td>
</tr>
<tr>
<td colspan="3">
<form method="get" class="xhsLft">
<input type="hidden" name="selected" value="%XHS_URL%">
<button class="xhsShopButton"><span class="fa fa-arrow-circle-left fa-fw"></span> <?php $this->label('continue_shopping');?></button>
</form>
<?php if($this->canOrder) { ?>
<form method="get" class="xhsRght">
<input type="hidden" name="selected" value="%XHS_URL%">
<input type="hidden" name="xhsCheckout" value="customersData">
<button class="xhsShopButton"><?php $this->label('next');?> <span class="fa fa-arrow-circle-right fa-fw"></span></button>
</form>
<?php } ?>
</td>
</tr>
</table>
<p>&nbsp;</p>
</section>
