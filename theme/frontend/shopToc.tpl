<?php if(count($this->categories) > 0) { ?>
<ul style="list-style-type: none;">
    <?php foreach($this->categories as $category){
        $class = (isset($this->selectedCategory) &&  $this->selectedCategory == $category['name'])
        ? 'xhsActiveCat' : 'xhsCat';
        ?>
    <li class="<?php echo $class; ?>">
        <a href="%SHOPURL%&xhsCategory=<?php echo $category['url']; ?>"><?php echo $category['name']; ?></a>
    </li>
    <?php  } ?>
</ul>
<?php } ?>

