<?php ?>
<?php $this->editProductLabel = isset($this->editProductLabel) ? $this->editProductLabel : 'new_product'; ?>
<ul id="xhsTaskTabs">
    <li class="%PRODUCTLIST%">
         <form method="post" class="tab">
             <input name="xhsTask" value="productList" type="hidden">
             <input value="<?php echo $this->labels['products_list'] ?>" class="subTab" type="submit">
         </form>
     </li>
     <li class="%EDITPRODUCT%">
         <form method="post" class="tab">
             <input name="xhsTask" value="editProduct" type="hidden">
             <input value="<?php echo $this->label($this->editProductLabel) ?>" class="subTab" type="submit">
         </form>
     </li>
     <li class="%PRODUCTCATEGORIES%">
         <form method="post" class="tab">
             <input name="xhsTask" value="productCategories" type="hidden">
             <input value="<?php echo $this->label('product_categories') ?>" class="subTab" type="submit">
         </form>
     </li>
    <li class="%HELPABOUT%">
        <form method="post" class="tab">
            <input name="xhsTask" value="helpAbout" type="hidden">
            <input value="<?php echo $this->labels['about'] ?>" class="tab" type="submit">
        </form>
    </li>
</ul>
