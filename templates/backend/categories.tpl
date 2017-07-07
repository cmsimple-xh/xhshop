<?php ?>
<section class="xhsMain xhsClearB">
	<h1><?php echo $this->labels['product_categories']; ?></h1>
	<form method="post">
		<p><strong><?php $this->label('use_category_selector'); ?></strong><br>
		<?php echo $this->radioNameValueLabel('xhsUseCats', 'true');?>&nbsp;<?php echo $this->label('yes') ?> 
		<?php echo $this->radioNameValueLabel('xhsUseCats', 'false');?>&nbsp;<?php $this->label('no') ?></p>
		<p><strong><?php $this->label('allow_show_all'); ?></strong><br>
		<?php echo $this->radioNameValueLabel('xhsAllowShowAll', 'true');?>&nbsp;<?php echo $this->label('yes') ?> 
		<?php echo $this->radioNameValueLabel('xhsAllowShowAll', 'false');?>&nbsp;<?php $this->label('no') ?></p>
		<div class="xhsRght">
			<input type="hidden" name="xhsTaskCat" value="product_tasks">
			<input type="hidden" name="xhsTask" value="saveProductCategories">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php $this->label('save_settings'); ?></button>
		</div>
	</form>
	<p>&nbsp;</p>
	<hr>
	<?php if(count($this->categories) > 0){ ?>
	<p><strong><?php $this->label('current_categories'); ?></strong><br>
	<?php $this->hint('current_categories') ; ?>
	</p>
	<table class="xhsTable">
		<?php foreach($this->categories as $index => $category){ ?>
		<tr>
			<td><?php if($index > 0){ ?>
				<form method="post">
					<input type="hidden" name="xhsMoveCat" value="<?php echo $index; ?>">
					<input type="hidden" name="xhsMoveDirection" value="up">
					<input type="hidden" name="xhsTask" value="saveProductCategories">
					<input type="hidden" name="xhsTaskCat" value="product_tasks">
					<button class="xhsProdUp" title="move category up"><span class="fa fa-chevron-up"></span></button>
				</form><?php } ?>
				 <?php if($index < count($this->categories)-1){ ?>
				<form method="post">
					<input type="hidden" name="xhsMoveCat" value="<?php echo $index; ?>">
					<input type="hidden" name="xhsMoveDirection" value="down">
					<input type="hidden" name = "xhsTask" value = "saveProductCategories">
					<input type="hidden" name = "xhsTaskCat" value = "product_tasks">
					<button class="xhsProdDown" title="move category down"><span class="fa fa-chevron-down"></span></button>
				</form><?php } ?>
			</td>
			<td>
				<form action = "" method = "post">
					<input type="text" name="xhsCatName" value="<?php echo $category; ?>" size ="30">&nbsp;
					<input type="hidden" name="xhsTask" value="saveProductCategories">
					<input type="hidden" name="xhsRenameCat">
					<input type="hidden" name="xhsTaskCat" value="product_tasks">
					<input type="hidden" name="xhsCatIndex" value="<?php echo $index; ?>">
					<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> OK</button>
				</form>
			</td>
		</tr><?php } ?>
	</table><?php } ?>
	<p>&nbsp;</p>
	<hr>
	<p><strong><?php echo $this->label('new_category'); ?></strong>
	<form action = "" method = "post">
		<input type="text" name="xhsAddCat" size="30">&nbsp;
		<input type="hidden" name="xhsTask" value="saveProductCategories">
		<input type="hidden" name="xhsTaskCat" value="product_tasks">
		<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> OK</button>
	</form>
	</p>
	<p><strong><?php $this->label('left_over_category'); ?></strong>
	<form method = "post">
		<input type="text" name="xhsLeftOverCat" value="<?php echo $this->leftOverCat;?>" size="30">&nbsp;
		<input type="hidden" name="xhsTask" value="saveProductCategories">
		<input type="hidden" name="xhsTaskCat" value="product_tasks">
		<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> OK</button>
	</form>
	</p>
	<?php if(count($this->categories) > 0){ ?>
	<p><strong><?php $this->label('default_product_category'); ?></strong><br>
	<?php $this->hint('default_product_category'); ?></p>
	<form method="post">
		<p><?php echo $this->radioNameValueLabel('xhsDefaultCat', $this->labels['all_categories']);?>&nbsp;<?php echo $this->label('all_categories') ?><br>
		<?php foreach($this->categories as $category){ ?>
		<?php echo $this->radioNameValueLabel('xhsDefaultCat', $category);?>&nbsp;<?php echo $category; ?><br>
        <?php } ?></p>
		<div class="">
			<input type="hidden" name="xhsTaskCat" value="product_tasks">
			<input type="hidden" name="xhsTask" value="saveProductCategories">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> OK</button>
		</div>
	</form>
	<?php } ?>
	<p>&nbsp;</p>
</section>
