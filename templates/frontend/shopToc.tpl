<?php if(count($this->categories) > 0) { ?>
<h2><i class="fa fa-shopping-cart fa-fw orange"></i> <?php $this->label('product_categories'); ?></h2>
<ul class="xhsCatMenu">
<?php foreach($this->categories as $category){
	$class = (isset($this->selectedCategory) &&  $this->selectedCategory == $category['name']) ? 'xhsActiveCat' : 'xhsCat';
?>
<li class="<?php echo $class; ?>">
<a href="%SHOPURL%&xhsCategory=<?php echo $category['url']; ?>"><?php echo $category['name']; ?></a>
</li>
<?php } ?>
</ul>
<?php } ?>

