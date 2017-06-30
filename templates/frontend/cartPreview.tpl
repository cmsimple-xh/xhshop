<?php ?>
<div id="xhsCartPreview">
<?php foreach($this->cartItems as $product){}?>
	<form action="%XHS_URL%" method="post">
		<button class="xhsShopButton xhsCrt" title="<?php echo $product['itemCounter']?> <?php echo $this->label('products'); ?> ⇒ <?php $this->label('go_to_cart'); ?>"><span class="fa fa-shopping-cart fa-2x"></span><span class="xhsItemCounter xhsBadge"><?php echo $product['itemCounter']; ?></span></button>
		<input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
		<input type = "hidden" name= "xhsCheckout" value="cart">
	</form>
</div>
