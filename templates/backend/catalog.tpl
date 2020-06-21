<?php ?>
<section class="xhsMain xhsClearB">
	<div class="xhsCatSel">
		<?php if($this->showCategorySelect !== false){ ?>
		<form class="catSelectBox" method="get">
			<input name="selected" value="<?php echo $this->page_url; ?>" type="hidden">
			<span class="fa fa-list fa-lg fa-fw"></span> <?php echo $this->categorySelect(); ?>
			<input name="xhsTask" value="productList" type="hidden" >
		</form>
		<?php } ?>
	</div>
	<h1 class="xhsCatHeader"><?php echo XH_hsc($this->categoryHeader) ?></h1>
	<?php if(count($this->products) == 0){ ?>
	<p><?php printf($this->hints['sold_out_back'], XH_hsc($this->categoryHeader)); ?></p>
	<?php return;} ?>
	<table id="xhsProductsTable" data-delete="<?php echo XH_hsc($this->hints['confirm_delete']) ;?>">
		<tbody>
		<?php
		$i = 0;
		foreach($this->products as $index=>$product){
			$previous = $i > 0 ? $this->indices[$i - 1] : null;
			$next = $i < count($this->products) - 1 ? $this->indices[$i + 1] : null; ?>
			<tr>
				<td class="">
					<form method="post" class="xhsMoveUp" <?php if (!isset($previous)) echo 'style="display: none"'?>>
						%CSRF_TOKEN_INPUT%
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsProductSwapID" value="<?php echo $previous; ?>">
						<input type="hidden" name="xhsTask" value="productList">
						<input type="hidden" name="xhsCategory" value="<?php echo $this->category; ?>">
						<button class="xhsProdUp" title="<?php echo $this->hints['move_up'] ;?>"><span class="fa fa-chevron-up"></span></button>
					</form>
					<form method="post" class="xhsInl xhsMoveDown" <?php if (!isset($next)) echo 'style="display: none"'?>>
						%CSRF_TOKEN_INPUT%
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsProductSwapID" value="<?php echo $next; ?>">
						<input type="hidden" name="xhsTask" value="productList">
						<input type="hidden" name="xhsCategory" value="<?php echo $this->category; ?>">
						<button class="xhsProdDown" title="<?php echo $this->hints['move_down'] ;?>"><span class="fa fa-chevron-down"></span></button>
					</form>
				</td>
				<td class="xhsTdTop"><strong><?php echo strip_tags($product['name']); ?></strong>
					<p class=""><?php echo $this->labels['price'];?> <?php echo $this->formatCurrency($product['price']); ?><br>
					<?php
					if(isset($product['variants'])){ ?>
					<?php $this->label('product_variants'); ?>:
					<?php echo implode('&nbsp;| ', $product['variants']); ?>
					<?php } ?>
					</p>
					<div>
					<?php if(isset($this->errors[$index])){ echo $this->productErrors($this->errors[$index]); }?>
					<?php if(isset($this->caveats[$index])){ echo $this->productHints($this->caveats[$index]); }?>
					</div>
				</td>
				<td>
					<form method="get" class="xhsInl">
						<input name="selected" value="%PAGE_URL%" type="hidden">
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsTask" value="editProduct">
						<button class="xhsProdDown" title="<?php echo $this->hint('edit_product'); ?>"><span class="fa fa-edit fa-lg"></span></button>
					</form>
					&nbsp;
					<form method="post" class="xhsInl xhsDeleteProduct" data-name="<?php echo XH_hsc(strip_tags($product['name'])) ;?>">
						%CSRF_TOKEN_INPUT%
						<input type="hidden" name="xhsProductID" value="<?php echo $index; ?>">
						<input type="hidden" name="xhsTask" value="deleteProduct">
						<button class="xhsProdDown" title="<?php echo $this->hint('delete_product') ;?>"><span class="fa fa-remove fa-lg"></span></button>
					</form>
				</td>
			</tr>
			<?php $i++; } ?>
		</tbody>
	</table>
	<p>&nbsp;</p>
</section>
