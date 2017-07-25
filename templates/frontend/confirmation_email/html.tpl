<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
body {
	font-family: Arial, Helvetica, sans-serif;
	padding: 1em;
}
p {
	margin: 0 0 .5em 0;
}
table {
	width: 40em;
	border-collapse: collapse;
	border-spacing: 0;
}
table.brd {
	width: 40em;
	border: 1px solid #ccc;
	border-collapse: collapse;
	border-spacing: 0;
}
table.brd td {
	border: 1px solid #ccc;
}
td {
	padding: .25em;
	vertical-align: top;
}
td.moneyCell {
	text-align: right;
	white-space: nowrap;
}
tr.brdT1 td {
	border-top: 1px solid #333;
}
tr.brdT2 td {
	border-top: 2px solid #333;
}
</style>
</head>
<body>
<div id="cartPreviews">
<p><?php echo $this->mail['salutation'];?> %FIRST_NAME% %LAST_NAME%,</p>
<p><?php echo nl2br($this->mail['thank_you']) ;?></p>
<p>&nbsp;</p>
<p><?php echo $this->mail['summary'] ;?></p>
<p>&nbsp;</p>
<table>
<tr>
<td style="width:30%"><?php echo $this->labels['date_of_order']; ?>:&nbsp;&nbsp;</td>
<td><?php echo date('d.m.Y [H:i]'); ?></td>
</tr>
<tr>
<td><?php echo $this->labels['delivery_adress']; ?>:&nbsp;&nbsp;</td>
<td>%FIRST_NAME% %LAST_NAME%<br>
%STREET%<br>
%ZIP_CODE% %CITY%<br>
%COUNTRY%<br>
%EMAIL%<br>
%PHONE%</td>
</tr>
<tr>
<td><?php $this->label('payment_mode') ?>:&nbsp;&nbsp;</td>
<td><?php echo $this->payment; ?></td>
</tr>
<tr>
<td><?php echo $this->labels['annotation'] ?>:&nbsp;&nbsp;</td>
<td>%ANNOTATION%</td>
</tr>
</table>
<p>&nbsp;</p>
<table class="brd">
<?php foreach($this->cartItems as $product){ ?>
<tr>
<td style="text-align: right;"><?php echo $product['amount'] ;?> x </td>
<td><?php echo strip_tags($product['name']);?> <?php echo $product['variantName']; ?></td>
<td style ="text-align: right; white-space: nowrap;padding-right: 1em;">&agrave; <?php echo $this->formatCurrency($product['price']) ;?></td>
<td class="moneyCell"><?php echo $this->formatCurrency($product['sum']) ;?></td>
</tr>
<?php } ?>
<tr class="brdT1">
<td colspan="3" style="text-align: right;"><?php echo $this->labels['subtotal'] ?></td>
<td class="moneyCell"><?php echo $this->formatCurrency($this->cartSum); ?></td>
</tr>
<tr>
<td colspan="3" style="text-align: right;"><?php echo $this->labels['forwarding_expenses'] ?></td>
<td class="moneyCell"><?php echo $this->formatCurrency($this->shipping); ?></td>
</tr>
<?php
if ($this->fee->isLessThan(Xhshop\Decimal::zero())) {
	$feeLabel = $this->labels['reduction'];
} else {
	$feeLabel = $this->labels['fee'];
} ?>
<tr>
<td colspan="3" style="text-align: right;"><?php echo $feeLabel; ?></td>
<td class="moneyCell"><?php echo $this->formatCurrency($this->fee); ?></td>
</tr>
<tr class="brdT2">
<td colspan="3" style="text-align: right;"><b><?php echo $this->labels['total'] ?></b></td>
<td class="moneyCell"><b><?php echo $this->formatCurrency($this->total); ?></b></td>
</tr>
</table>
<p>&nbsp;</p>
<?php if ($this->hideVat == false):?>
<p><?php echo $this->labels['included_vat'] . ' ' . $this->formatCurrency($this->vatTotal); ?> (<?php echo $this->formatFloat($this->reducedRate); ?>% = <?php echo $this->formatCurrency($this->vatReduced); ?> – <?php echo $this->formatFloat($this->fullRate); ?>% = <?php echo $this->formatCurrency($this->vatFull); ?>)</p>
<?php else:?>
<p><?php echo $this->hints['price_info_no_vat']?></p>
<?php endif?>
<p>&nbsp;</p>
<?php echo $this->mail['gtc']?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><?php echo $this->mail['greetings']; ?><br>
%CONTACT_NAME%</p>
</div>
</body>
</html>