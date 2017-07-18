<?php ?>
<div id="xhsCartPreview">
	<form action="%XHS_URL%" method="post">
		%CSRF_TOKEN_INPUT%
		<button class="xhsShopButton xhsCrt" title="<?php echo $product['itemCounter']?> <?php echo $this->label('products'); ?> ⇒ <?php $this->label('go_to_cart'); ?>"><span class="fa fa-shopping-cart fa-2x"></span><span class="xhsItemCounter xhsBadge"><?php echo $product['itemCounter']; ?></span></button>
		<input type = "hidden" name= "xhsCheckout" value="cart">
	</form>
</div>
