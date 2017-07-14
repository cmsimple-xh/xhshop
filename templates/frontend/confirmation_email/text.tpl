<?php $feeLabel = ($this->fee < 0) ? $this->labels['reduction'] : $this->labels['fee']; ?>
<?php echo $this->mail['salutation'];?> %FIRST_NAME% %LAST_NAME%,

<?php echo $this->mail['thank_you']; ?>

<?php echo $this->mail['summary']; ?>

<?php $this->label('date_of_order'); ?>: <?php echo date('d.m.Y [H:i]'); ?>

<?php $this->label('delivery_adress'); ?>:
%FIRST_NAME% %LAST_NAME%
%STREET%
%ZIP_CODE% %CITY%
%COUNTRY%
%EMAIL%
%PHONE%


<?php $this->label('payment_mode');?>: %PAYMENT%

<?php $this->label('annotation');?>:
%ANNOTATION%

------------------------------------------------------------
<?php
foreach($this->cartItems as $product){
    echo $product['amount'] . ' x '. strip_tags($product['name']) . ' ' . $product['variantName'] .  ' à '
        . $this->formatCurrency($product['price']) . "\n"
        . '= ' . $this->formatCurrency($product['sum']) . "\n------------------------------------------------------------\n";
    } ?>
<?php $this->label('subtotal'); ?>: <?php echo $this->formatCurrency($this->cartSum); ?> 
<?php $this->label('forwarding_expenses');?>: <?php echo $this->formatCurrency($this->shipping); ?> 
<?php echo $feeLabel;?>: <?php echo $this->formatCurrency($this->fee); ?> 
--------------------------------------------------------------
<?php $this->label('total');?>: <?php echo $this->formatCurrency($this->total); ?> 
--------------------------------------------------------------
<?php if($this->hideVat == false):?>
<?php $this->label('included_vat');?> <?php echo $this->formatCurrency($this->vatTotal); ?> 
(<?php echo $this->formatFloat($this->reducedRate); ?>%: <?php echo $this->formatCurrency($this->vatReduced); ?> / <?php echo $this->formatFloat($this->fullRate); ?> %: <?php echo $this->formatCurrency($this->vatFull); ?>)
<?php else:?>
<?php echo $this->hints['price_info_no_vat']?>
<?php endif?>

 
<?php echo $this->mail['greetings']; ?>%CONTACT_NAME%