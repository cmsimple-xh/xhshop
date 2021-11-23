<?php ?>
<section class="xhsMain">
<h1><?php $this->label('checkout_overview'); ?></h1>
<div id="xhsSteps">
<div class="xhsStep">1. <?php $this->label('checkout_step1'); ?></div>
<div class="xhsStep xhsNow">2. <?php $this->label('checkout_step2'); ?></div>
<div class="xhsStep">3. <?php $this->label('checkout_step3'); ?></div>
</div>
<div class="xhsClearB"></div>
<div class="xhsStepHint"><span class="fa fa-question-circle fa-fw"></span> <?php $this->label('ask_for_contact_data'); ?></div>
<h2><?php $this->label('ask_for_destination');?></h2>
<form action="%XHS_CHECKOUT_URL%" method="post" class="xhs100">
<?php echo $this->salutationSelectbox(); ?>
<?php echo $this->contactInput('first_name'); ?>
<?php echo $this->contactInput('last_name'); ?>
<?php echo $this->contactInput('street'); ?>
<?php echo $this->contactInput('extra_address_line'); ?>
<?php echo $this->contactInput('zip_code'); ?>
<?php echo $this->contactInput('city'); ?>
<?php echo $this->countriesSelectbox(); ?>
<?php echo $this->contactInput('phone'); ?>
<?php echo $this->contactInput('email'); ?>
<label>
<input type="hidden" name="may_forward_email" value="0">
<input type="checkbox" name="may_forward_email" value="1" <?php if ($_SESSION['xhsCustomer']->may_forward_email) echo 'checked'; ?>>
<?php $this->label('may_forward_email'); ?></label>
<p> </p>
<!-- start payment modes -->
<h2><?php echo $this->paymentHint(); ?></h2>
<?php foreach($this->payments as $module){
$checked = $_SESSION['xhsCustomer']->payment_mode == $module->getName() ?  ' checked="checked"' : '';
$fee = '';
if (!$module->getFee()->isEqualTo(Xhshop\Decimal::zero())) {
	$fee = ' (' . $this->formatCurrency($module->getFee()) . ' ';
	$fee .= $module->getFee()->isLessThan(Xhshop\Decimal::zero()) ? '<strong>' . $this->labels['reduction'] . '</strong>' : $this->labels['fee'];
	$fee .= ')';
} ?>
<label>
	<input type="radio" name="payment_mode" value="<?php echo $module->getName(); ?>"<?php echo $checked; ?><?php if (in_array('payment_mode', $this->requiredCustomerData)) echo 'required'; ?>>
<?php echo $module->getLabel() . $fee ?>
</label><br>
<?php } ?>
<!-- end payment modes -->
<p> </p>
<!--Start C.O.S-confirmation -->
<?php
if(in_array('cos_confirmed', $this->missingData)){
    $cosLabel = '<span class="xhsRequired">' . $this->labels['confirm_gtc'] . '</span>';
} else {
    $cosLabel = $this->labels['confirm_gtc'];}
?>
<h2><?php echo $cosLabel; ?></h2>
<p><input type="checkbox" name="cos_confirmed" required <?php if($_SESSION['xhsCustomer']->cos_confirmed =='on') {echo 'checked="checked"';} ?>> <?php echo $this->cosHint();?></p>
<!-- End C.O.S. -->
<!-- Start annotation -->
<h2><?php $this->label('ask_for_annotation'); ?></h2>
<p><?php $this->hint('ask_for_annotation'); ?></p>
<textarea name="annotation"><?php echo $_SESSION['xhsCustomer']->annotation; ?></textarea>
<!-- End annotation -->
<p> </p>
%CSRF_TOKEN_INPUT%
<button class="xhsShopButton xhsRght xhsInl"><?php $this->label('next'); ?> <span class="fa fa-arrow-circle-right fa-fw"></span></button>
</form>
<form method="get" class="xhsLft">
<?php if (strlen(XHS_URL) > 0): ?>
		<input type="hidden" name="selected" value="%XHS_URL%">
<?php endif; ?>
<input type="hidden" name="xhsCheckout" value="cart">
<button class="xhsShopButton xhsInl"><span class="fa fa-arrow-circle-left fa-fw"></span> <?php $this->label('back'); ?></button>
</form>
<p>&nbsp;</p>
</section>
