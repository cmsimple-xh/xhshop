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
<form action="" method="post" class="xhs100">
<?php echo $this->contactInput('first_name'); ?>
<?php echo $this->contactInput('last_name'); ?>
<?php echo $this->contactInput('street'); ?>
<?php echo $this->contactInput('email'); ?>
<?php echo$this->contactInput('phone'); ?>
<?php echo $this->contactInput('zip_code'); ?>
<?php echo $this->contactInput('city'); ?>
<?php echo $this->countriesSelectbox(XHS_LANGUAGE); ?>
<p> </p>
<!-- start payment modes -->
<h2><?php echo $this->paymentHint(); ?></h2>
<?php foreach($this->payments as $module){
if(!$module->isActive()){continue;}
$checked = $_SESSION['xhsCustomer']->payment_mode == $module->getName() ?  ' checked="checked"' : '';
$fee = '';
if((float)$module->getFee() !== 0.00){
	$fee = ' (' . $this->formatCurrency((float)$module->getFee()) . ' ';
	$fee .= (float)$module->getFee() < 0 ? '<b>' . $this->labels['reduction'] . '</b>' : $this->labels['fee'];
	$fee .= ')';
} ?>
<label>
	<input type="radio" name="payment_mode" value="<?php echo $module->getName(); ?>"<?php echo $checked; ?>>
<?php echo $module->getLabel() . $fee ?>
</label><br>
<?php } ?>
<!-- end payment modes -->
<p> </p>
<!--Start C.O.S-confirmation -->
<?php
if(in_array('cos_confirmed', $this->missingData)){
    $cosLabel = '<span class="xhsRequired">' . $this->labels['confirm_cos'] . '</span>';
} else {
    $cosLabel = $this->labels['confirm_cos'];}
?>
<h2><?php echo $cosLabel; ?></h2>
<p><input type="checkbox" name="cos_confirmed" <?php if($_SESSION['xhsCustomer']->cos_confirmed =='on') {echo 'checked="checked"';} ?>> <?php echo $this->cosHint();?></p>
<!-- End C.O.S. -->
<!-- Start annotation -->
<h2><?php $this->label('ask_for_annotation'); ?></h2>
<p><?php $this->hint('ask_for_annotation'); ?></p>
<textarea name="annotation"><?php echo $_SESSION['xhsCustomer']->annotation; ?></textarea>
<!-- End annotation -->
<p> </p>
<div class="xhsRght">
<input type="hidden" name="xhsCheckout" value="checkCustomersData">
<button class="xhsShopButton xhsRght xhsInl"><?php $this->label('next'); ?> <span class="fa fa-arrow-circle-right fa-fw"></span></button>
</form>
<form action="" method="post">
<input type="hidden" name="xhsCheckout" value="cart">
<button class="xhsShopButton xhsInl"><span class="fa fa-arrow-circle-left fa-fw"></span> <?php $this->label('previous'); ?></button>
</form>
<p>&nbsp;</p>
</section>
