<?php ?>
<a class="xhsShopButton" href="?<?php echo XHS_URL;?>"><span class="fa fa-list fa-lg fa-fw"></span> <?php echo $this->labels['products_list']; ?></a>
<section class="xhsMain xhsClearB">
	<h1><?php echo $this->labels['edit_product']; ?></h1>
	<form method = "post" class="xhsProductForm">
		<p><strong><?php $this->label('product_name'); ?>:</strong><br>
		<?php echo $this->textInputNameValueLabel('xhsName', '%NAME%', null, array('size' => 60)); ?></p>
        <p><strong><?php $this->label('product_variants'); ?>:</strong><br>
		<?php $this->hint('product_variants'); ?><br>
		<?php echo $this->textInputNameValueLabel('xhsVariants', '%VARIANTS%', array('size' => '60')); ?></p>
		<p>
		<table class="xhsTable">
			<tr>
				<td style="min-width:30%;"><strong><?php $this->label('price'); ?>:</strong></td>
				<td><?php echo $this->moneyInputNameValueLabel('xhsPrice', $this->price, array('size' => '10')); ?></td>
			</tr>
			<tr>
				<td><strong><?php $this->label('vat_rate'); ?>:</strong></td>
				<td><?php echo $this->radioNameValueLabel('vat', 'full', 'full_vat'); ?> <?php echo $this->radioNameValueLabel('vat', 'reduced', 'reduced_vat'); ?></td>
			</tr>
			<tr>
				<td><strong><?php printf($this->labels['shipping_unit'], $this->shipping_unit); ?>:</strong></td>
				<td><?php echo $this->floatInputNameValueLabel('xhsWeight', $this->weight, array('size' => '10')); ?></td>
			</tr>
			<tr>
				<td><strong><?php $this->label('available');?>:</strong></td>
				<td><?php echo $this->radioNameValueLabel('stockOnHand', 1, 'yes'); ?> <?php echo $this->radioNameValueLabel('stockOnHand', 0, 'no'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><hr></td>
			</tr>
            <tr>
				<td class="xhsTdTop"><strong><?php $this->label('preview_picture');?>:</strong></td>
				<td class="xhsTdTop">%PREVIEW_SELECTOR% <span id="xhsPreviewPic">%PREVIEW%</span></td>
			</tr>
			<tr>
				<td class="xhsTdTop"><strong><?php $this->label('product_picture');?>:</strong></td>
				<td class="xhsTdTop">%IMAGE_SELECTOR% <span id="xhsImage">%IMAGE%</span></td>
			</tr>
			<tr>
				<td class="xhsTdTop"><strong><?php $this->label('internal_link'); ?>:</strong><br><?php echo $this->hint('multi_selection'); ?></td>
				<td class="xhsTdTop"><?php echo $this->productPageSelector(); ?></td>
			</tr>
			<tr <?php if (!$this->use_categories) echo 'style="display:none"'; ?>>
				<td class="xhsTdTop"><strong><?php $this->label('product_categories'); ?>:</strong><br><?php echo $this->hint('multi_selection'); ?></td>
				<td class="xhsTdTop"><?php echo $this->productCategorySelector(); ?></td>
			</tr>
			<tr>
				<td colspan="2"><hr></td>
			</tr>
		</table>
		<p><strong><?php $this->label('product_teaser'); ?>:</strong><br>
		<?php $this->hint('product_teaser'); ?><p>
		<textarea name="xhsTeaser" id="xhsTeaser" class="xhsTeaser" cols="50" rows="5">%TEASER%</textarea>
        <p><strong><?php $this->label('product_description'); ?>:</strong><br>
		<?php $this->hint('product_description'); ?></p>
		<textarea name="xhsDescription" id="xhsDescription" class="xhsDescription" cols="50" rows="15">%DESCRIPTION%</textarea>
		<p>&nbsp;</p>
			<input type="hidden" name="xhsProductID" value="<?php echo $this->product_ID; ?>">
			<input type="hidden" name="xhsTask" value="saveProduct">
			<button class="xhsShopButton xhsRght"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
			%CSRF_TOKEN_INPUT%
	</form>
	<p class="xhsClearB">&nbsp;</p>
</section>
