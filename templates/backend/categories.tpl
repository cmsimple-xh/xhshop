<?php ?>
<section class="xhsMain xhsClearB">
	<h1><?php echo $this->labels['product_categories']; ?></h1>
	<?php if(count($this->categories) > 0){ ?>
	<p><strong><?php $this->label('current_categories'); ?></strong><br>
	<?php $this->hint('current_categories') ; ?>
	</p>
	<table class="xhsTable">
		<?php foreach($this->categories as $index => $category){ ?>
		<tr>
			<td><?php if($index > 0){ ?>
				<form method="post">
					%CSRF_TOKEN_INPUT%
					<input type="hidden" name="xhsMoveCat" value="<?php echo $index; ?>">
					<input type="hidden" name="xhsMoveDirection" value="up">
					<input type="hidden" name="xhsTask" value="saveProductCategories">
					<button class="xhsProdUp" title="<?php echo $this->hints['move_up'] ;?>"><span class="fa fa-chevron-up"></span></button>
				</form><?php } ?>
				 <?php if($index < count($this->categories)-1){ ?>
				<form method="post">
					%CSRF_TOKEN_INPUT%
					<input type="hidden" name="xhsMoveCat" value="<?php echo $index; ?>">
					<input type="hidden" name="xhsMoveDirection" value="down">
					<input type="hidden" name = "xhsTask" value = "saveProductCategories">
					<button class="xhsProdDown" title="<?php echo $this->hints['move_down'] ;?>"><span class="fa fa-chevron-down"></span></button>
				</form><?php } ?>
			</td>
			<td>
				<form method="post">
					%CSRF_TOKEN_INPUT%
					<input type="text" name="xhsCatName" value="<?php echo $category; ?>" size ="30">&nbsp;
					<input type="hidden" name="xhsTask" value="saveProductCategories">
					<input type="hidden" name="xhsRenameCat">
					<input type="hidden" name="xhsCatIndex" value="<?php echo $index; ?>">
					<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php echo $this->labels['save_settings'] ;?></button>
				</form>
			</td>
		</tr><?php } ?>
	</table><?php } ?>
	<p>&nbsp;</p>
	<hr>
	<p><strong><?php echo $this->label('new_category'); ?></strong></p>
	<form method="post">
		%CSRF_TOKEN_INPUT%
		<input type="text" name="xhsAddCat" size="30">&nbsp;
		<input type="hidden" name="xhsTask" value="saveProductCategories">
		<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php echo $this->labels['save_settings'] ;?></button>
	</form>
	<p>&nbsp;</p>
	<p><strong><?php $this->label('left_over_category'); ?></strong></p>
	<form method = "post">
		%CSRF_TOKEN_INPUT%
		<input type="text" name="xhsLeftOverCat" value="<?php echo $this->leftOverCat;?>" size="30">&nbsp;
		<input type="hidden" name="xhsTask" value="saveProductCategories">
		<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php echo $this->labels['save_settings'] ;?></button>
	</form>
	<?php if(count($this->categories) > 0){ ?>
	<p>&nbsp;</p>
	<p><strong><?php $this->label('default_product_category'); ?></strong><br>
	<?php $this->hint('default_product_category'); ?></p>
	<form method="post">
		%CSRF_TOKEN_INPUT%
		<?php if ($this->allowShowAll){ ?>
		<p><label><?php echo $this->radioNameValueLabel('xhsDefaultCat', $this->labels['all_categories']);?>&nbsp;<?php echo $this->label('all_categories') ?></label><br>
		<?php } ?>
		<?php foreach($this->categories as $category){ ?>
		<label><?php echo $this->radioNameValueLabel('xhsDefaultCat', $category);?>&nbsp;<?php echo $category; ?></label><br>
        <?php } ?></p>
		<div class="">
			<input type="hidden" name="xhsTask" value="saveProductCategories">
			<button class="xhsShopButton"><span class="fa fa-save fa-fw"></span> <?php echo $this->labels['save_settings'] ;?></button>
		</div>
	</form>
	<?php } ?>
	<p>&nbsp;</p>
</section>
