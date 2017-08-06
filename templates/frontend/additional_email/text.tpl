<?php echo $this->mail['salutation'];?> %FIRST_NAME% %LAST_NAME%,

nochmals vielen Dank für Ihre unten aufgeführte Bestellung.
Ich hoffe, daß die Ware Ihnen gefallen wird und bedanke mich für das Vertrauen,
das Sie mir entgegen gebracht haben.

Ich habe Ihre Bestellung heute verschickt.
Die Lieferung sollte in 1 - 2 Werktagen ankommen - wenn nicht bitte ich um Nachricht,
ich kann dann die Sendungsverfolgung aufrufen.

Die (ggf. quittierte) Rechnung liegt im Paket.

Hinweis:
Bitte öffnen Sie das Paket immer an der oberen Klebenaht und schneiden Sie mit dem Cuttermesser nicht zu tief!

<?php echo "\n------------------------------------------------------------\n"; ?>

Wir schickten Ihnen laut Bestellung vom <?php echo date('d.m.Y'); ?>

<?php
foreach($this->cartItems as $product){
    echo $product['amount'] . ' x '. strip_tags($product['name']) . ' ' . $product['variantName'] .  ' à '
        . $this->formatCurrency($product['price']) . "\n"
        . '= ' . $this->formatCurrency($product['sum']) . "\n------------------------------------------------------------\n";
    } ?>


Liebe Grüße und jederzeit gerne wieder!

%CONTACT_NAME%
