<?php $xhsSearch = isset($_POST['xhsProductSearch']) ? $_POST['xhsProductSearch'] : ''; ?>
<div class="xhsCatSelSearch">
	<div class="xhsCatSel">
		<?php if($this->showCategorySelect !== false){ ?>
		<form method="get" class="catSelectBox">
			<input name="selected" value="<?php echo $this->page_url; ?>" type="hidden">
			<span class="fa fa-list fa-lg fa-fw"></span> <?php echo $this->categorySelect(); ?>
			<input name="xhsTask" value="productList" type="hidden">
		</form>
		<?php } ?>
	</div>
	<div class="xhsSearch">
		<form method="get">
			<input name="selected" value="<?php echo $this->page_url; ?>" type="hidden">
			<span class="fa fa-search fa-lg fa-fw"></span>
			<input name="xhsProductSearch" value="<?php echo $xhsSearch; ?>" type="text" placeholder="<?php $this->label('product_search'); ?>">
		</form>
	</div>
	<div class="xhsClearB"></div>
</div>
<section id="xhsOverview" class="xhsMain">
	<?php if($this->categoryHeader != ''){ ?>
	<h1 class="xhsCatHeader"><?php echo $this->categoryHeader; ?></h1>
	<?php } else { ?>
	<h1 class="xhsCatHeader"><?php echo $this->label('all_categories'); ?></h1>
	<?php }; ?>
	<?php if(count($this->products) === 0){ ?>
	<p>
		<?php
		if(isset($_POST['xhsProductSearch'])){
			printf($this->hints['no_products_found'], $_POST['xhsProductSearch'] );
		} else {
			printf($this->hints['sold_out'],$this->categoryHeader);
		} ?>
	</p>
	<?php return;
} ?>
	<?php foreach($this->products as $index => $product){ ?>
	<article class="xhsOverviewPrds">
		<h2 class="xhsProdTitle"><?php echo $product['name']; ?></h2>
		<div class="xhsPrevPic"><?php echo $product['previewPicture']; ?></div>
		<div class="xhsPrdTeaser"><?php echo $product['teaser']; ?></div>
		<div class="xhsDetailLink"><?php echo $product['detailLink']; ?></div>
		<div class="xhsInfoBlock">
			<form method="post" >
				<?php if(isset($product['variants'])){ ?>
				<div class="xhsVariantsSelect"><span class="xhsPrdPriceLabel"><?php echo $this->labels['product_variants'];?></span>
					<select name="xhsVariant">
						<?php foreach($product['variants'] as $key => $variant){ ?>
						<option value="<?php echo $key; ?>"><?php echo $variant; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php } ?>
			<div class="xhsPrdPrice"><span class="xhsPrdPriceLabel"><?php echo $this->labels['price'];?></span> <span class="xhsPrdPriceNum"><?php echo $this->formatCurrency($product['price']); ?></span></div>
				<input class="xhsInpAmount" type="number" min="1" step="1" name="xhsAmount" value="1">
				<input type="hidden" name="xhsTask" value="updateCart" />
				<input type="hidden" name="xhsCategory" value="<?php echo $this->selectedCategory; ?>">
				x
				<input type="hidden" name="cartItem" value="<?php echo $index; ?>" />
				<button class="xhsShopButton xhsAddCart"><span class="fa fa-cart-plus fa-lg fa-fw"></span> <?php echo $this->label('add_to_cart'); ?></button>
				%CSRF_TOKEN_INPUT%
			</form>
		</div>
	</article>
	<?php } ?>
</section>
