<?php ?>
<a class="xhsShopButton" href="?<?php echo XHS_URL;?>"><span class="fa fa-list fa-lg fa-fw"></span> <?php echo $this->labels['products_list']; ?></a>
<article class="xhsMain xhsPrdDetails" vocab="http://schema.org/" typeof="Product">
	<h1 class="xhsProdTitle" property="name">%NAME%</h1>
	<div class="xhsPrdDetTeaser">%TEASER%</div>
	<div class="xhsPrevPic">%IMAGE%</div>
	<meta property="image" content="%PREVIEWPICTURE%">
	<div class="xhsProdDescript" property="description">%DESCRIPTION%</div>
	<div class="xhsInfoBlock">
		<form method="post">
			%CSRF_TOKEN_INPUT%
			<input type="hidden" name="xhsTask" value="updateCart">
			<input type="hidden" name="cartItem" value="<?php echo $this->uid ; ?>">
			<?php if($this->variants){ ?>
			<div class="xhsVariantsSelect"><span class="xhsPrdPriceLabel"><?php echo $this->labels['product_variants'];?></span>
				<select name="xhsVariant">
					<?php  foreach($this->variants as $index => $variant){ ?>
					<option value="<?php echo $index; ?>"><?php echo $variant; ?></option>
					<?php }?>
				</select>
			</div>
			<?php } ?>
			<div class="xhsPrdPrice" property="offers" typeof="Offer">
				<meta property="price" content="<?php echo $this->price; ?>">
				<meta property="priceCurrency" content="<?php echo $this->currency; ?>">
				<meta property="availability" content="http://schema.org/InStock">
				<span class="xhsPrdPriceLabel"><?php echo $this->labels['price']; ?><br>
				<?php $this->hint($this->vatInfo); ?> <?php if (!$this->hideVat) echo $this->formatPercentage($this->vatRate) ; ?><br><?php echo $this->shippingCostsHint(); ?></span> <span class="xhsPrdPriceNum"><?php echo $this->formatCurrency($this->price); ?></span></div>
			<input class="xhsInpAmount" type="number" min="1" step="1" name="xhsAmount" value="1">
			x
			<button class="xhsShopButton xhsAddCart"><span class="fa fa-cart-plus fa-lg fa-fw"></span> <?php echo $this->label('add_to_cart'); ?></button>
		</form>
	</div>
</article>
