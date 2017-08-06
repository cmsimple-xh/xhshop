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
<p>
	nochmals vielen Dank für Ihre unten aufgeführte Bestellung.
	Ich hoffe, daß die Ware Ihnen gefallen wird und bedanke mich für das Vertrauen,
	das Sie mir entgegen gebracht haben.
</p>
<p>
	Ich habe Ihre Bestellung heute verschickt.
	Die Lieferung sollte in 1 - 2 Werktagen ankommen - wenn nicht bitte ich um Nachricht,
	ich kann dann die Sendungsverfolgung aufrufen.
</p>
<p>
	Die (ggf. quittierte) Rechnung liegt im Paket.
</p>
<p>
	<strong>Hinweis:</strong><br>
	Bitte öffnen Sie das Paket immer an der oberen Klebenaht und schneiden Sie mit dem Cuttermesser nicht zu tief!
</p>

<p>&nbsp;</p>
<hr>
<p>&nbsp;</p>
<p>Wir schickten Ihnen laut Bestellung vom <?php echo date('d.m.Y'); ?></p>

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
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>Liebe Grüße und jederzeit gerne wieder!<br>
%CONTACT_NAME%</p>
</div>
</body>
</html>
