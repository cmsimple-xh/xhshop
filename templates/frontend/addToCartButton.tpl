<?php ?>
<section id="cartButton" class="xhsMain">
	<h1 class="xhsInl xhsLft"><?php echo $this->productName; ?></h1>
	<div class="xhsInfoBlock">
		<div class="xhsPrdPrice"><span class="xhsPrdPriceLabel"><?php echo $this->labels['price'];?></span> <span class="xhsPrdPriceNum"><?php echo $this->formatCurrency($this->product->price); ?></span></div>
		<div class="price_info"><span class="xhsPrdPriceLabel">
			 <?php $this->hint($this->vatInfo); ?> (<?php echo $this->formatFloat($this->vatRate) ?> %)<br>
            <?php $this->hint('price_info_shipping'); ?>  
			<br></span>
		</div>
		<form action="" method="post">
			<input type="hidden" name="xhsTask" value="updateCart">
			<input type="hidden" name="cartItem" value="<?php echo $this->product->uid ; ?>">
			<input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
			<?php if(isset($this->variants)){ ?>
			<select name="xhsVariant">
				<?php  foreach($this->variants as $index => $variant){ ?>
				<option value="<?php echo $index; ?>"><?php echo $variant; ?></option>
				<?php }?>
			</select>
			<?php } ?>
			<input class="xhsInpAmount" type="number" min="1" max="100" step="1" name="xhsAmount" value="1">
			x
			<button class="xhsShopButton xhsAddCart"><span class="fa fa-cart-plus fa-lg fa-fw"></span> <?php echo $this->labels['add_to_cart']; ?></button>
		</form>
	</div>
</section>
