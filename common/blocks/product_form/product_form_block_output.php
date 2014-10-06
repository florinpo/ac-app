


<?php
//$bla = YII_DEBUG ? false : 'asset' . $path . '-1';

//echo Yii::getPathOfAlias('common.layouts.default.assets');

//var_dump(Yii::app()->themeManager);
// we remove the  imagesProduct session on page refresh
if (app()->user->hasState('imagesProduct')) {
    app()->user->setState('imagesProduct', null);
}

$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.bTooltip',
    'options' => array(
        'position' => 'right',
        'trigger' => 'hover',
        'speed' => '150',
        'theme' => '.tooltipster-green'
        ))
);

$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.t-right',
    'options' => array(
        'position' => 'right',
        'trigger' => 'hover',
        'speed' => '150'
        ))
);

$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.t-top',
    'options' => array(
        'trigger' => 'hover',
        'speed' => '150'
        ))
);

$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.t-bottom',
    'options' => array(
        'trigger' => 'hover',
        'speed' => '150',
        'position' => 'bottom'
        ))
);

$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.t-cancel',
    'options' => array(
        'content' => t('site', 'Cancel'),
        'position' => 'right',
        'trigger' => 'hover',
        'speed' => '150'
        ))
);
?>
<section id="product-add-edit">
    <div class="box_round_c grid_19 omega">
        <h1>
            <?php
            echo isset($product) ? t('site', 'Update product') : t('site', 'Add new product');
            ?>
        </h1>

        <div class="form-wrapper">

            <p class="info">
                <?php echo t('site', 'Per aggiungere un prodotto o un servizio devi completare i campi qui sotto
            (I campi con <span class="red">*</span> sono obbligatori):') ?>
            </p>

            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'product-form',
                'htmlOptions' => array('autocomplete' => 'off', 'enctype' => 'multipart/form-data', 'class' => 'product-form'),
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'clientOptions' => array(
                    'validationDelay' => 50,
                    'validateOnSubmit' => true,
                    'validateOnChange' => false,
                    'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
                    )));
            ?>

            <div class="row first clearfix">
                <div class="grid_4">
                    <label for="name" class="required alignR"><?php echo t('site', 'Product name:'); ?></label>
                </div>
                <div class="grid_8">
                    <?php echo $form->textField($model, 'name'); ?>
                </div>
                <div class="grid_1">
                    <a class="bTooltip t-left" href="javascript:void(0);" title="<?php echo t('site', 'Max 60 caratteri.'); ?>"><span class="icon icon-info-sign"></span></a>
                </div>
                <div class="clear"></div>
                <?php echo $form->error($model, 'name'); ?>
            </div>

            <div class="row clearfix">
                <div class="grid_4">
                    <label for="model" class="alignR"><?php echo t('site', 'Brand:'); ?></label>
                </div>
                <div class="grid_5">
                    <?php echo $form->textField($model, 'model'); ?>
                </div>

                <div class="clear"></div>
                <?php echo $form->error($model, 'model'); ?>
            </div>

            <div class="row clearfix">
                <div class="grid_4">
                    <label for="section_store" class="alignR"><?php echo t('site', 'Product section:'); ?></label>
                </div>
                <div class="grid_5">
                    <?php echo $form->dropDownList($model, 'section_store', ProductSale::getSectionsForm($shop->id), array('class' => 'jq-selectbox')); ?>
                </div>
                <div class="clear"></div>
                <?php echo $form->error($model, 'section'); ?>
            </div>
            <div class="hr-dashed"></div>
            <div class="row clearfix">
                <div class="grid_4">
                    <label for="price" class="required alignR"><?php echo t('site', 'Regular price:'); ?></label>
                </div>
                <div class="grid_3">
                    <?php echo $form->textField($model, 'price'); ?>
                </div>
                <div class="grid_4">
                    <span class="hint-i"><?php echo t('site', '<strong>EURO</strong> (I.V.A. included)') ?></span>
                </div>
                <div class="clear"></div>
                <?php echo $form->error($model, 'price'); ?>
            </div>

            <div class="row clearfix discount">
                <?php
                $checked = isset($product) && $product->discount_price > 0 ? 'checked' : false;
                $classPanel = ($checked == 'checked') ? 'active' : 'hidden';
                ?>
                <div class="grid_4">
                    <br />
                </div>
                <div class="grid_8">
                    <?php echo $form->checkBox($model, 'has_discount', array('checked' => $checked, 'class' => 's-checkbox')); ?>
                    <label for="has_discount"><?php echo t('site', 'Do you offer discount ?'); ?></label>
                </div>
            </div>
            <div class="op-panel <?php echo $classPanel; ?>">
                <div class="row clearfix">
                    <div class="grid_4">
                        <label for="discount_rate" class="required alignR"><?php echo t('site', 'Discount rate:'); ?></label>
                    </div>
                    <div class="grid_3">
                        <?php echo $form->textField($model, 'discount_rate', array('class' => 'i-200')); ?>
                    </div>
                    <div class="grid_4">
                        <span class="hint-i p-rate">%</span>
                    </div>
                    <div class="clear"></div>
                    <?php echo $form->error($model, 'discount_rate'); ?>
                </div>
                <div class="row clearfix">
                    <div class="grid_4">
                        <label for="discount_price" class="required alignR"><?php echo t('site', 'New price:'); ?></label>
                    </div>
                    <div class="grid_3">
                        <?php echo $form->textField($model, 'discount_price', array('class' => 'r-only', 'readOnly' => true)); ?>
                    </div>
                    <div class="grid_4">
                        <span class="hint-i"><?php echo t('site', '<strong>EURO</strong>') ?></span>
                    </div>
                    <div class="clear"></div>
                    <?php echo $form->error($model, 'discount_price'); ?>
                </div>
                <div class="row clearfix">
                    <div class="grid_4">
                        <label for="min_quantity" class="required alignR"><?php echo t('site', 'Min quantity:'); ?></label>
                    </div>
                    <div class="grid_3">
                        <?php echo $form->textField($model, 'min_quantity'); ?>
                    </div>
                    <div class="clear"></div>
                    <?php echo $form->error($model, 'min_quantity'); ?>
                </div>
                <div class="row clearfix">
                    <div class="grid_4">
                        <label for="discount_duration" class="required alignR"><?php echo t('site', 'Discount availability:'); ?></label>
                    </div>
                    <?php
                    $dates = ConstantDefine::getDiscountDuration();
                    if (isset($product) && !empty($product->expire_time)) {
                        $days_left = floor(($product->expire_time - time()) / (60 * 60 * 24)) . ' ' . t('site', 'days');
                        $dates = array_put_to_position($dates, $days_left, 0);
                    }
                    ?>
                    <div class="grid_3"> 
                        <?php
                        echo $form->dropDownList($model, 'discount_duration', $dates, array('class' => 'jq-selectbox'));
                        ?>
                    </div>

                    <div class="clear"></div>
                    <?php echo $form->error($model, 'discount_duration'); ?>
                </div>
            </div>

            <div class="hr-dashed"></div>

            <div class="row clearfix">
                <?php echo $form->hiddenField($model, 'selected_cats', array('type' => "hidden")); ?>
                <div class="grid_4">
                    <label for="selected_cats" class="required alignR"><?php echo t('site', 'Product categories:'); ?></label>
                </div>


                <div class="grid_4">
                    <?php
                    $domains = ProductSaleCategoryList::getDomains(false);

                    //var_dump($domains);
                    echo $form->dropDownList($model, 'domain_id', $domains, array('data-placeholder' => "Choose the domain", 'tabindex' => 1, 'class' => 'jq-selectbox-ajax', 'empty' => '', 'options' => array($model->domain_id => array('selected' => true))));
                    //var_dump($domains);
//                    echo Chosen::activeDropDownList($model, 'domain_id', $domains, array('options' => array(
//                            'disableSearch' => true
//                            )));
                    ?>
                </div>
                <div class="grid_7">
                    <?php
                    $selected_categories = array();

                    if (isset($product)) {
                        foreach ($product->categories as $cat_id) {
                            $selected_categories[$cat_id->id] = array('selected' => 'selected');
                        }
                    }

                    //echo  Chosen::activeMultiSelect($model, '', $data, $htmlOptions);
                    echo $form->dropDownList($model, 'category_id', isset($product) ? ProductSaleCategoryList::getCategories($product->domain_id, false) : array(), array(
                        'options' => $selected_categories,
                        'multiple' => 'multiple'

                            //'class' => 'listBox',
                            //'empty' => '-Select category-',
                    ));
                    ?>
                </div>
                <div class="clear"></div>
                <?php echo $form->error($model, 'domain_id'); ?>
                <?php echo $form->error($model, 'category_id'); ?>
            </div>

            <div class="row">
                <div class="grid_4">
                    <label for="description" class="required alignR"><?php echo t('site', 'Descrizione:'); ?></label>
                </div>

                <div class="grid_11 txtaread-wrap">
                    <?php echo $form->textArea($model, 'description', array('rows' => 8)); ?>
                </div>
                <div id="count"></div>
                <div class="clear"></div>
                <?php echo $form->error($model, 'description'); ?>
            </div>

            <div class="row clearfix">
                <div class="grid_4">
                    <label for="tags" class="required alignR"><?php echo t('site', 'Product tags:'); ?></label>
                </div>
                <div class="grid_11">
                    <?php echo $form->hiddenField($model, 'tags'); ?>
                    <ul id="product-lbs" class="c-tags floatL"></ul>
                </div>
                <div class="clear"></div>
                <?php echo $form->error($model, 'tags'); ?>
            </div>

            <div class="hr-dashed"></div>

            <div class="row">
                <div class="grid_4">
                    <label for="description" class="required alignR"><?php echo t('site', 'Immagini del prodotto:'); ?></label>
                </div>
                <div class="grid_11 omega">
                    <div class="upload-block">
                        <?php
                        $uploadWidget = $this->widget('cms.extensions.xupload.XUpload', array(
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
                            'formView' => 'common.blocks.product_form._upload_form',
                            'uploadView' => 'common.blocks.product_form.upload_views.upload',
                            'downloadView' => 'common.blocks.product_form.upload_views.download',
                            'options' => array(
                                'maxNumberOfFiles' => "js:$.productForm.maxNumberOfFiles",
                                'maxFileSize' => 300000,
                                'minFileSize' => ConstantDefine::UPLOAD_MIN_SIZE,
                                'autoUpload' => false,
                                'sequentialUploads' => true,
                                'acceptFileTypes' => "js:/(\.|\/)(jpe?g|png|gif)$/i"
                            ),
                            'parentId' => ($product != null) ? $product->id : null,
                                ));
                        ?>

                    </div>
                </div>
                <div class="clear"></div>
            </div>

            <div class="hr-dashed"></div>

            <div class="row actions">
                <?php echo CHtml::submitButton(t('site', 'Save'), array('class' => 'buttonM bGreen', 'id' => 'saveBtn')); ?>

            </div>

            <?php $this->endWidget(); ?>
        </div>
    </div>
</section>
