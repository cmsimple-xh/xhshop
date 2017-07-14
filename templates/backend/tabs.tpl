<?php ?>
<?php $this->editProductLabel = isset($this->editProductLabel) ? $this->editProductLabel : 'new_product'; ?>
<ul id="xhsTaskTabs">
    <li class="%PRODUCT_TASKS%">
        <form method="post" class="tab">
            <input name="xhsTask" value="productList" type="hidden">
            <input name="xhsTaskCat" value="product_tasks" type="hidden">
            <input value="<?php echo $this->labels['products'] ?>" class="tab" type="submit">
        </form>
        <ul class="xhsSubTabs" id="xhsProducts">
           <li class="%PRODUCTLIST%">
                <form method="post" class="tab">
                    <input name="xhsTask" value="productList" type="hidden">
                    <input name="xhsTaskCat" value="product_tasks" type="hidden">
                    <input value="<?php echo $this->labels['products_list'] ?>" class="subTab" type="submit">
                </form>
            </li>
            <li class="%EDITPRODUCT%">
                <form method="post" class="tab">
                    <input name="xhsTask" value="editProduct" type="hidden">
                    <input name="xhsTaskCat" value="product_tasks" type="hidden">
                    <input value="<?php echo $this->label($this->editProductLabel) ?>" class="subTab" type="submit">
                </form>
            </li>
            <li class="%PRODUCTCATEGORIES%">
                <form method="post" class="tab">
                    <input name="xhsTask" value="productCategories" type="hidden">
                    <input name="xhsTaskCat" value="product_tasks" type="hidden">
                    <input value="<?php echo $this->label('product_categories') ?>" class="subTab" type="submit">
                </form>
            </li>
        </ul>
    </li>
    <li class="%HELP_TASKS%">
        <form method="post" class="tab">
            <input name="xhsTask" value="helpAbout" type="hidden">
            <input name="xhsTaskCat" value="help_tasks" type="hidden">
            <input value="<?php echo $this->labels['about'] ?>" class="tab" type="submit">
        </form>
    </li>
</ul>
