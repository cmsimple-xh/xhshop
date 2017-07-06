<?php ?>
<section class="xhsMain xhsClearB">
	<div class="xhsCatSel">
		<form class="catSelectBox" action="" method="post">
			<span class="fa fa-list fa-lg fa-fw"></span> <?php echo $this->categorySelect(); ?>
			<input name="xhsTask" value="productList" type="hidden" >
			<input name="xhsTaskCat" value="product_tasks" type="hidden">
		</form>
	</div>
	<h1 class="xhsCatHeader"><?php echo $this->categoryHeader ?></h1>
	<?php if(count($this->products) == 0){ ?>
	<p><?php printf($this->hints['sold_out'], strip_tags($this->categoryHeader)); ?></p>
	<?php return;} ?>
	<table id="xhsProductsTable">
		<tbody>
		<?php
		$i = 0;
		foreach($this->products as $index=>$product){
			$previous = $i > 0 ? $this->indices[$i - 1] : null;
			$next = $i < count($this->products) - 1 ? $this->indices[$i + 1] : null; ?>
			<tr>
				<td class="">
				<?php if(isset($previous)){?>
					<form action="" method="post">
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsProductSwapID" value="<?php echo $previous; ?>">
						<input type="hidden" name="xhsTask" value="productList">
						<input type="hidden" name="xhsTaskCat" value="product_tasks">
						<input type="hidden" name="xhsCategory" value="<?php echo $this->category; ?>">
						<button class="xhsProdUp" title="swap sort index with previous product"><span class="fa fa-chevron-up"></span></button>
					</form>
				<?php }
				else { ?>
						<!--<button class="xhsProdUp"><span class="fa"> </span></button>-->
				<?php } ?>
				<?php if(isset($next)){?>
					<form action="" method="post" class="xhsInl">
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsProductSwapID" value="<?php echo $next; ?>">
						<input type="hidden" name="xhsTask" value="productList">
						<input type="hidden" name="xhsTaskCat" value="product_tasks">
						<input type="hidden" name="xhsCategory" value="<?php echo $this->category; ?>">
						<button class="xhsProdDown" title="swap sort index with previous product"><span class="fa fa-chevron-down"></span></button>
					</form>
				<?php } ?>
				</td>
				<td class="xhsTdTop"><strong><?php echo strip_tags($product['name']); ?></strong>
					<p class=""><?php echo $this->labels['price'];?> <?php echo $this->formatCurrency($product['price']); ?><br>
					<?php
					if(isset($product['variants'])){ ?>
					<?php $this->label('product_variants'); ?>:
					<?php echo implode('&nbsp;| ', $product['variants']); ?>
					<?php } ?>
					</p>
					<p>
					<?php if(isset($this->errors[$index])){ echo $this->productErrors($this->errors[$index]); }?>
					<?php if(isset($this->caveats[$index])){ echo $this->productHints($this->caveats[$index]); }?>
					</p>
				</td>
				<td>
					<form action="" method="post" class="xhsInl">
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsTask" value="editProduct">
						<input type="hidden" name="xhsTaskCat" value="product_tasks">
						<button class="xhsProdDown" title="edit product"><span class="fa fa-edit fa-lg"></span></button>
					</form>
					&nbsp;
					<form action="" method="post" class="xhsInl" onsubmit="return xhsAssureDelete('<?php echo addslashes(strip_tags($product['name'])); ?>');">
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsTask" value="deleteProduct">
						<input type="hidden" name="xhsTaskCat" value="product_tasks">
						<button class="xhsProdDown" title="delete product"><span class="fa fa-remove fa-lg"></span></button>
					</form>
				</td>
			</tr>
			<?php $i++; } ?>
		</tbody>
	</table>
	<p>&nbsp;</p>
</section>
