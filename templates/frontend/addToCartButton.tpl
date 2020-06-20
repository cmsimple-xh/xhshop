<?php ?>
<section class="cartButton xhsMain">
	<h1 class="xhsInl xhsLft"><?php echo $this->productName; ?></h1>
	<div class="xhsInfoBlock">
		<div class="xhsPrdPrice"><span class="xhsPrdPriceLabel"><?php echo $this->labels['price'];?></span> <span class="xhsPrdPriceNum"><?php echo $this->formatCurrency($this->product->getGross()); ?></span></div>
		<div class="price_info"><span class="xhsPrdPriceLabel">
			 <?php $this->hint($this->vatInfo); ?> (<?php echo $this->formatPercentage($this->vatRate) ?>)<br>
            <?php echo $this->shippingCostsHint(); ?>  
			<br></span>
		</div>
		<form method="post">
			%CSRF_TOKEN_INPUT%
			<input type="hidden" name="xhsTask" value="updateCart">
			<input type="hidden" name="cartItem" value="<?php echo $this->product->getUid() ; ?>">
			<?php if(isset($this->variants)){ ?>
			<select name="xhsVariant">
				<?php  foreach($this->variants as $index => $variant){ ?>
				<option value="<?php echo $index; ?>"><?php echo $variant; ?></option>
				<?php }?>
			</select>
			<?php } ?>
			<input class="xhsInpAmount" type="number" min="1" step="1" name="xhsAmount" value="1">
			x
			<button class="xhsShopButton xhsAddCart"><span class="fa fa-cart-plus fa-lg fa-fw"></span> <?php echo $this->labels['add_to_cart']; ?></button>
		</form>
	</div>
</section>
