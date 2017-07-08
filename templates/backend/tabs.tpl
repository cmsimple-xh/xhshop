<?php ?>
<div class="appNmVrs">
<p><span class="fa fa-shopping-cart fa-2x"></span>&nbsp;&nbsp;%APP_NAME%, Version %VERSION%</p>
</div>
<?php $this->editProductLabel = isset($this->editProductLabel) ? $this->editProductLabel : 'new_product'; ?>
<ul id="xhsTaskTabs">
    <li class="%SETTING_TASKS%">
        <form method="post" class="tab">
            <input name="xhsTask" value="shippingSettings" type="hidden">
            <input name="xhsTaskCat" value="setting_tasks" type="hidden">
            <input value="<?php echo $this->labels['settings'] ?>" class="tab" type="submit">
        </form>
        <ul class="xhsSubTabs" id="xhsSettings">
            <li class="%SHIPPINGSETTINGS%">
                <form method="post" class="tab">
                    <input name="xhsTask" value="shippingSettings" type="hidden">
                    <input name="xhsTaskCat" value="setting_tasks" type="hidden">
                    <input value="<?php echo $this->labels['shipping']; ?>" class="subTab" type="submit">
                </form>
            </li>
            <li class="%PAYMENTSETTINGS%">
                <form method="post" class="tab">
                    <input name="xhsTask" value="paymentSettings" type="hidden">
                    <input name="xhsTaskCat" value="setting_tasks" type="hidden">
                    <input value="<?php echo $this->labels['payments'] ?>" class="subTab" type="submit">
                </form>
            </li>
        </ul>
    </li>
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
