<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
$defaultImage = $layout_asset . "/images/entries/products/180/no-image.png";
?>


<section id="product-add-edit" class="box_round_c grid_19 omega">
    <h1 class="dash"><?php echo t('site', 'Aggiungi un prodotto per vendita'); ?></h1>
    <div class="form-box">
        <p class="info">
            <?php echo t('site', 'Per aggiungere un prodotto o un servizio devi completare i campi qui sotto
            (I campi con <span class="red">*</span> sono obbligatori):') ?>
        </p>

        <?php
        $this->widget('cms.extensions.tooltipster.tooltipster', array(
            'identifier' => '.t-middle',
            'options' => array(
                'position' => 'top',
                'trigger' => 'hover',
                'speed' => '150',
                'theme' => '.tooltipster-yellow'
                ))
        );
        ?>
        <?php
        $this->widget('cms.extensions.tooltipster.tooltipster', array(
            'identifier' => '.t-bottom-left',
            'options' => array(
                'position' => 'bottom-left',
                'trigger' => 'hover',
                'speed' => '150',
                'theme' => '.tooltipster-yellow'
                ))
        );
        ?>
        <?php
        $this->widget('cms.extensions.tooltipster.tooltipster', array(
            'identifier' => '.t-left',
            'options' => array(
                'position' => 'right',
                'trigger' => 'hover',
                'speed' => '150',
                'theme' => '.tooltipster-yellow'
                ))
        );
        ?>

        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'product-form',
            'htmlOptions' => array('autocomplete' => 'off', 'enctype' => 'multipart/form-data', 'class' => 'product-form'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validationDelay' => 50,
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                //'validateOnType' => true,
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
                )));
        ?>
        <div class="box-form upload">
            <div class="row first">
                <label for="uploadimg"><?php echo t('site', 'Immagini del prodotto:'); ?></label>
                <div class="upload-box">


                    <div class="uploaded-list clearfix">
                        <p class="info-h"><?php echo t('site', 'Ataseaza max. 3 imagini ale produsului:') ?></p>

                        <ul class="items-80-form clearfix">
                            <?php for ($i = 0; $i <= 2; $i++): ?>
                                <li id="item-<?php echo $i; ?>">
                                    <?php if (isset($product->pimages)): ?>
                                        <?php if (isset($product->pimages[$i])): ?>
                                            <?php if ($product->pimages[$i] != null && $product->pimages[$i]->path != ''): ?>
                                                <?php
                                                $class = 'tmb';
                                                if ($product->mainImage == $product->pimages[$i]->id) {
                                                    $class = 'tmb selected';
                                                }
                                                ?>
                                                <div class="thumbnail">
                                                    <img class="<?php echo $class; ?>" src="<?php echo IMAGES_URL . '/img180/' . $product->pimages[$i]->path; ?>"/>
                                                </div>
                                                <div class="actions-u clearfix">
                                                    <?php
                                                    echo CHtml::ajaxLink(CHtml::image($layout_asset . '/images/icons/btn-actions/grey/x-13-tight.png', ''), Yii::app()->createUrl('productsale/deleteimg'), array(
                                                        'type' => 'POST',
                                                        'dataType' => 'json',
                                                        'data' => array(
                                                            'img_id' => $product->pimages[$i]->id,
                                                            'counter' => $i,
                                                            'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                                                        ),
                                                        'success' => "function(data) {
                                                        if(data.success==1){
//                                                            $('li#item-'+ data.counter + ' .thumbnail img').attr('src', '" . $defaultImage . "');
//                                                            $('li#item-'+ data.counter + ' .deleteImg').hide();
//                                                            $('li#item-'+ data.counter + ' .thumbImg').hide();
//                                                            $('#upload_form').removeClass('hidden').addClass('visible');
//                                                            
//                                                             var inputCount = $('input#maxFilesCounter');
//                                                             inputCount.val(+inputCount.val()+1);
                                                           
                                                             window.parent.location.href = '" . Yii::app()->createUrl('page/render', array('slug' => plaintext($_GET['slug']), 'id' => $product->id)) . "';
                                                            
                                                        } else {
                                                            alert(" . t('site', '"Error while deleting the image"') . ");
                                                        }
                                                    }"), array(
                                                        'class' => 'deleteImg t-middle',
                                                        'href' => 'javascript:void(0)', 'confirm' => t('site', 'Are you sure you want to delete this image?'),
                                                        'title' => t('site', 'Cancela immagine')
                                                    ));
                                                    ?>
                                                    <?php
                                                    if ($product->imagescount > 1) {
                                                        echo CHtml::ajaxLink(
                                                                CHtml::image($layout_asset . '/images/icons/upload/cover.png', ''), Yii::app()->createUrl('productsale/mainimg'), array(
                                                            'type' => 'POST',
                                                            'dataType' => 'json',
                                                            'data' => array(
                                                                'img_id' => $product->pimages[$i]->id,
                                                                'prod_id' => $product->id,
                                                                'counter' => $i,
                                                                'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                                                            ),
                                                            'success' => "function(data) {
                                                                if(data.success==1){
                                                                   $('ul.items-80-form li .thumbnail img').removeClass('selected');
                                                                   $('li#item-'+ data.counter + ' .thumbnail img').addClass('selected');
                                                                } else {
                                                                    alert(" . t('site', '"Error while updating the image"') . ");
                                                                }
                                                            }"), array(
                                                            "class" => "thumbImg t-middle",
                                                            'href' => 'javascript:void(0)',
                                                            'title' => t('site', 'Immagine principale')
                                                        ));
                                                    }
                                                    ?> 
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="thumbnail">
                                                <img src="<?php echo $defaultImage; ?>"/>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="thumbnail">
                                            <img src="<?php echo $defaultImage; ?>"/>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php endfor; ?>
                        </ul> 
                    </div>

                    <?php
                    if (!isset($product) || (isset($product) && $product->imagescount < 3)) {
                        $class = 'visible';
                    } else {
                        $class = 'hidden';
                    }
                    ?>


                    <div id="upload_form" class="upload-form <?php echo $class; ?>">
                        <?php
                        $maxFiles = 3;
                        echo CHtml::hiddenField('maxFilesCounter', isset($product) && $product->imagescount <= $maxFiles ? $maxFiles - $product->imagescount : 0);
                        ?>
                        <?php
                        $this->widget('cms.extensions.xupload.XUpload', array(
                            'url' => Yii::app()->createUrl("productsale/upload"),
                            //our XUploadForm
                            'model' => $files,
                            //We set this for the widget to be able to target our own form
                            'htmlOptions' => array('id' => 'product-form'),
                            'attribute' => 'uploadimg',
                            'multiple' => true,
                            //Note that we are using a custom view for our widget
                            //Thats becase the default widget includes the 'form' 
                            //which we don't want here
                            'formView' => 'common.blocks.product_addedit_sale.upload_form',
                            'uploadView' => 'cms.extensions.xupload.views.multiple.upload',
                            'downloadView' => 'cms.extensions.xupload.views.multiple.download',
                            'options' => array(
                                'maxNumberOfFiles' => isset($product) && $product->imagescount <= $maxFiles ? $maxFiles - $product->imagescount : $maxFiles,
                                //'maxNumberOfFiles' =>'js:$("#maxFilesCounter").val()',
                                'maxFileSize' => ConstantDefine::UPLOAD_MAX_SIZE,
                                'minFileSize' => ConstantDefine::UPLOAD_MIN_SIZE,
                                'autoUpload' => true,
                                'sequentialUploads' => true,
                                'acceptFileTypes' => "js:/(\.|\/)(jpe?g|png|gif)$/i",
                                'added' => 'js:function (e, data) {
                                     $("a.tooltip").tooltipster("hide");
                                }',
                                )));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-form">
            <div class="row">
                <label for="name" class="required"><?php echo t('site', 'Product name:'); ?></label>
                <?php echo $form->textField($model, 'name', array()); ?>
                <a class="tooltip t-left" title="<?php echo t('site', 'Max 60 caratteri.'); ?>"></a>
                <?php echo $form->error($model, 'name'); ?>
            </div>
            <div class="row">
                <label for="name"><?php echo t('site', 'Product model:'); ?></label>
                <?php echo $form->textField($model, 'model', array()); ?>
                <?php echo $form->error($model, 'model'); ?>
            </div>

            <div class="row">
                <label for="tags" class="required"><?php echo t('site', 'Product tags:'); ?></label>
                <?php echo $form->textField($model, 'tags', array()); ?>
                <?php echo $form->error($model, 'tags'); ?>
            </div>
            <div class="row">
                <label for="section"><?php echo t('site', 'Product section:'); ?></label>
                <?php echo $form->dropDownList($model, 'section', ProductSale::getSectionsForm($shop->id), array()); ?>
                <?php
                echo CHtml::link('<span class="inner"><span class="text">'
                        . t('site', 'Aggiungi sezione')
                        . '<span class="icon-plus-circle icon-grey"></span></span></span>', 'javascript:void(0)', array(
                    'class' => 'btn-s grey addSection'
                ));
                ?>
               
                <?php echo $form->error($model, 'section'); ?>
            </div>
        </div>
        <div class="box-form">
            <div class="row">
                <label for="price" class="required"><?php echo t('site', 'Prezzo:'); ?></label>
                <?php echo $form->textField($model, 'price', array('class' => 'i-200')); ?>
                <span class="hint-i"><?php echo t('site', '<strong>EURO</strong> (I.V.A. incluso)') ?></span>
                <?php echo $form->error($model, 'price'); ?>
            </div>
            <div class="row discount">
                <?php
                $checked = isset($product) && $product->discount_price > 0 ? 'checked' : false;
                $classPanel = ($checked == 'checked') ? 'active' : 'hidden';
                ?>
                <?php echo $form->checkBox($model, 'has_discount', array('checked' => $checked)); ?>
                <label for="has_discount"><?php echo t('site', '(Optional) Acordati Reduceri?'); ?></label>

            </div>
            <div class="op-panel <?php echo $classPanel; ?>">
                <div class="row">
                    <label for="discount_rate" class="required"><?php echo t('site', 'Sconto:'); ?></label>
                    <?php echo $form->textField($model, 'discount_rate', array('class' => 'i-200')); ?>
                    <span class="hint-i p-rate">%</span>
                    <a class="tooltip t-left" title="<?php echo t('site', 'Trebuie sa fie de min 10% si maxim 90%'); ?>"></a>
                    <?php echo $form->error($model, 'discount_rate'); ?>
                </div>
                <div class="row">
                    <label for="discount_price" class="required"><?php echo t('site', 'Prezzo scontato:'); ?></label>
                    <?php echo $form->textField($model, 'discount_price', array('class' => 'i-200 r-only', 'readOnly' => true)); ?>
                    <span class="hint-i"><?php echo t('site', '<strong>EURO</strong>') ?></span>
                    <?php echo $form->error($model, 'discount_price'); ?>
                </div>
                <div class="row">
                    <label for="min_quantity" class="required"><?php echo t('site', 'Quantita minima:'); ?></label>
                    <?php echo $form->textField($model, 'min_quantity', array('class' => 'i-200')); ?>
                    <?php echo $form->error($model, 'min_quantity'); ?>
                </div>
                <div class="row">
                    <label for="discount_duration" class="required"><?php echo t('site', 'Durata dell sconto:'); ?></label>
                    <?php
                    $dates = ConstantDefine::getDiscountDuration();
                    if (isset($product) && !empty($product->expire_time)) {
                        $days_left = floor(($product->expire_time - time()) / (60 * 60 * 24)) . ' ' . t('site', 'giorni rimanenti');
                        $dates = array_put_to_position($dates, $days_left, 0);
                    }

                    echo CHtml::activeDropDownList($model, 'discount_duration', $dates, array('class' => 'i-200', 'options' => array()));
                    ?>
                    <?php echo $form->error($model, 'discount_duration'); ?>
                </div>
            </div>
        </div>
        <div class="box-form">
            <div class="row clearfix">
                <label for="selected_cats" class="required"><?php echo t('site', 'Product categories:'); ?></label>
                <?php echo $form->hiddenField($model, 'selected_cats', array('type' => "hidden")); ?>
                <?php
                echo Chtml::listBox('listboxSelected', array(), isset($product) ? ProductSale::getSelectedCategories($product->id) : array(), array('multiple' => 'multiple', 'class' => 'listBox', 'size' => 5)
                );
                ?>
                <div class="actions">
                    <?php if (isset($product)): ?>
                        <?php
                        echo CHtml::link('<span class="inner"><span class="text">'
                                . t('site', 'Scegliere')
                                . '<span class="icon-plus-circle icon-grey"></span></span></span>', 'javascript:void(0)', array(
                            'class' => 'btn-s grey',
                            'name' => 'addCategory',
                            'onclick' => "popWindow('" . Yii::app()->controller->createUrl('page/render', array('slug' => 'product-sale-categories', 'id' => $product->id)) . "','_blank',1000,630,1,1)"
                        ));
                        ?>
                    <?php else: ?>
                        <?php
                        echo CHtml::link('<span class="inner"><span class="text">'
                                . t('site', 'Scegliere')
                                . '<span class="icon-plus-circle icon-grey"></span></span></span>', 'javascript:void(0)', array(
                            'class' => 'btn-s grey',
                            'name' => 'addCategory',
                            'onclick' => "popWindow('" . Yii::app()->controller->createUrl('page/render', array('slug' => 'product-sale-categories')) . "','_blank',1000,630,1,1)"
                        ));
                        ?>
                    <?php endif; ?>
                    <?php
                    echo CHtml::link('<span class="inner"><span class="text">'
                            . t('site', 'Cancela')
                            . '<span class="icon-x-circle icon-grey"></span></span></span>', 'javascript:void(0)', array('id' => 'delete_cat_copy', 'class' => 'btn-s grey'));
                    ?>
                </div>
                <a class="tooltip t-bottom-left" title="<?php echo t('site', 'Enumerati serviciile si/sau produsele oferite<br /> de compania ta in maxim 200 caractere.'); ?>"></a>
                <?php echo $form->error($model, 'selected_cats'); ?>
            </div>
            <div class="row clearfix">
                <label for="description" class="required"><?php echo t('site', 'Description:'); ?></label>
                <div class="txtaread-wrap">
                    <?php echo $form->textArea($model, 'description', array('rows' => 6)); ?>
                </div>
                <a class="tooltip t-bottom-left" title="<?php echo t('site', 'Enumerati serviciile si/sau produsele oferite<br /> de compania ta in maxim 200 caractere.'); ?>"></a>
                <?php echo $form->error($model, 'description'); ?>
            </div>
        </div>
        <div class="box-form-a">
            <div class="actions">
                <?php echo CHtml::submitButton(t('site', 'Salva'), array('class' => 'btn-i-n i-green', 'id' => 'saveBtn')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>

<?php
//app()->clientScript->registerScriptFile($layout_asset . '/js/bootstrap/bootstrap-image-gallery.min', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/ckeditor/ckeditor.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/ckeditor/adapters/jquery.js", CClientScript::POS_HEAD);
?>

<?php
Yii::app()->clientScript->registerScript('productForm', '
      $("#delete_cat_copy").live("click",function() {
            var selected = $("#listboxSelected option:selected");
            selected.remove();
            var list =[];
            
            $("#listboxSelected option").each(function (index) {
                list.push(this.value);
            });
            $("#ProductSaleForm_selected_cats").val(list);
            return false;
        });
        $("#product-form").submit(function() {
            var list = [];
            $("#listboxSelected").find("option").each(function() {
                list.push(this.value);
            });
            $("#ProductSaleForm_selected_cats").val(list);
        });
        
        $("#ProductSaleForm_discount_rate, #ProductSaleForm_min_quantity").keyup(function() {
          
            if (!isNaN(parseInt(this.value,10))) {
                this.value = parseInt(this.value);
            } else {
                this.value = "";
            }
            this.value = this.value.replace(/[^0-9]/g, "");
         });
         
        // price calculation
        $("#ProductSaleForm_discount_rate, #ProductSaleForm_price").keyup(function() {
            var fullPrice = parseFloat($("#ProductSaleForm_price").val());
            var discountRate = parseFloat($("#ProductSaleForm_discount_rate").val());
            var discountPrice = fullPrice - (fullPrice*(discountRate/100));
            
            if(discountPrice % 1 != 0){
              discountPrice = parseFloat(discountPrice).toFixed(2);
            } else {
              discountPrice = Math.round(discountPrice);
            }
            if($("#ProductSaleForm_discount_rate").val()!="" && $("#ProductSaleForm_price").val()!="" && isNumber($("#ProductSaleForm_price").val())){
                if(discountRate < 10 || discountRate > 90){
                $("#ProductSaleForm_discount_price").val("");
               // return false;
                } else {
                   $("#ProductSaleForm_discount_price").val(discountPrice);
                }
            } else 
            $("#ProductSaleForm_discount_price").val("");
        });
        
       //toggle items
        if($("#ProductSaleForm_has_discount").is(":checked"))
        {
            $(".op-panel").removeClass("hidden").addClass("active");  // checked
        }
        else
        {
            $(".op-panel").removeClass("active").addClass("hidden");  // unchecked
        }

        $("#ProductSaleForm_has_discount").click(function(){
            $(".op-panel").toggle(this.checked);
        });
        
        // CKEditor configuration
         var config = {
            height: 100,
            resize_minWidth: 425,
            resize_maxWidth: 425,
            width : 425,
            language: "it",
            toolbar :
                [
                ["Bold","BulletedList"]
            ]
        };
        // Set for the CKEditor
        $("#ProductSaleForm_description").ckeditor(config);
        
', CClientScript::POS_READY);
?>


