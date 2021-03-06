<section id="categories-select" class="box_round_c_green grid_24">
    <h2 class="dash"><?php echo t('site', 'Scegli le categorie del prodotto'); ?></h2>
    <div class="form-wrapper">
        <p class="info"><?php echo t('site', 'Attenzione! Selezionare il settore, categoria e sotto-categoria, quindi premere il tasto "Select" e poi "Salva":') ?></p>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'product-category-form',
            'htmlOptions' => array('class' => 'categories-form'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                //'validationDelay' => 0,
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
                'beforeValidate' =>'js:function(form){
                    $(".loader").show();
                     return true;
                 }',
                'afterValidate' => 'js:function(form,data,hasError){
                        $(".loader").hide();
                        if(!hasError){
                                $.ajax({
                                        "type":"POST",
                                        "url":"' . CHtml::normalizeUrl(array("productsale/selectedcategories")) . '",
                                        "data":{"id":send(), "YII_CSRF_TOKEN":"' . Yii::app()->getRequest()->getCsrfToken() . '"},
                                        "beforeSend": function() {
                                            $(".loader").show();
                                        },
                                        "complete": function(){
                                            $(".loader").hide();
                                        },
                                        "success":function(data){
                                            var $response=$(data).val();
                                            var list = [];
                                            if (!$("#listbox_categories").find("option[value="+$response+"]").length > 0) {
                                                $("#listbox_categories").append(data);
                                                $("#listbox_categories option").each(function (index) {
                                                    list.push(this.value);
                                                 });
                                                $("#ProductSaleCategoryForm_selected_categories").val(list);
                                            };
                                        }
                                 });
                         }
                 }'
            ),
                ));
        ?>
        <div class="box-form clearfix">
            <div class="row">
                <label for="domain_id"><?php echo t('site', 'Select domain'); ?></label>
                <?php
                echo $form->listBox($model, 'domain_id', ProductSaleCategoryList::getDomains(false), array(
                    'class' => 'listBox',
                    'size' => 10,
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => Yii::app()->createUrl('productsale/updatecategories'),
                        'data' => array('domain_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                        'update' => '#ProductSaleCategoryForm_category_id',
                    ),
                    'options' => array($model->domain_id => array('selected' => true))
                ));
                ?>
                <?php echo $form->error($model, 'domain_id'); ?>
            </div>
            <div class="row">
                <label for="category_id"><?php echo t('site', 'Select category'); ?></label>
                <?php
                echo $form->listBox($model, 'category_id', array(), array(
                    'class' => 'listBox',
                    'size' => 10,
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => Yii::app()->createUrl('productsale/updatesubcategories'),
                        'data' => array('category_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                        'update' => '#ProductSaleCategoryForm_subcategory_id',
                        )));
                ?>
                <?php echo $form->error($model, 'category_id'); ?>
            </div>
            <div class="row last">
                <label for="category_id"><?php echo t('site', 'Select subcategory'); ?></label>
                <?php
                echo $form->listBox($model, 'subcategory_id', array(), array(
                    'class' => 'listBox',
                    'size' => 10,
                ));
                ?>
                <?php echo $form->error($model, 'subcategory_id'); ?>
            </div>
        </div>
        <div class="box-form-a clearfix">
            <div class="actions">
                <div class="loader"></div>
                <?php echo Chtml::submitButton(t('site', 'Select Category'), array('id' => 'select_cat', 'name' => 'select-product-cat', 'class' => 'btn-i-s i-grey icon-arrow-down')); ?>
                <?php echo Chtml::submitButton(t('site', 'Salva'), array('id' => 'saveCats', 'name' => 'save-product-cat', 'class' => 'btn-i-n i-green')); ?>

            </div>
        </div>
        <div class="box-form last clearfix">
            <div class="row selected">
                <label for="selected_categories"><?php echo t('site', 'Hai scelto:'); ?></label>
                <?php echo $form->hiddenField($model, 'selected_categories', array('type' => "hidden", 'value' => !empty($product->categoryIds) ? implode(',', $product->categoryIds) : '')); ?>
                <?php
                echo Chtml::listBox('listbox_categories', array(), isset($product) ? ProductSale::getSelectedCategories($product->id) : array(), array(
                    'multiple' => 'multiple',
                    'class' => 'listBox',
                    'size' => 5,
                ));
                ?>
                <?php echo $form->error($model, 'selected_categories'); ?>
            </div>
            <div class="actions">
                <?php echo Chtml::button(t('site', 'Cancela'), array('id' => 'delete_cat', 'class' => 'btn-i-s i-grey icon-x-circle')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>

<?php
Yii::app()->clientScript->registerScript('selectProductCat', ' 
        function send() {
            var id;
            if ($("#ProductSaleCategoryForm_category_id").val() != null && $("#ProductSaleCategoryForm_subcategory_id").val() == null){
                id = $("#ProductSaleCategoryForm_category_id").val();
            }

            else if ($("#ProductSaleCategoryForm_category_id").val() != null && $("#ProductSaleCategoryForm_subcategory_id").val() !== null){
                id = $("#ProductSaleCategoryForm_subcategory_id").val();
            }
            return id;
        }
    
       $("#saveCats").click(function(){
            var catSelected = $("#listbox_categories");
            var catList = $("#listboxSelected", window.opener.document);
            catList.find("option").remove();
            catSelected.children().each(function(){
                catList.append($("<option></option>").attr("value",$(this).val()).text($(this).text()));
            });
            $(".close").trigger("click");					
        });
        
       $("#delete_cat").live("click",function() {
            var selected = $("#listbox_categories option:selected");
            selected.remove();
            var list =[];
            $("#listbox_categories option").each(function (index) {
                list.push(this.value);
            });
            $("#ProductSaleCategoryForm_selected_categories").val(list);
            return false;
        });
        
        $(".close").click(function(){
            window.close();
        });
', CClientScript::POS_READY);
?>