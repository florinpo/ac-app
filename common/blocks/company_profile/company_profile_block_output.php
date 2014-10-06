
<section id="member-shop" class="box_widget grid_18 omega">
    <h1 class="b-dotted"><?php echo t('site', 'Profilo impressa'); ?></h1>
    <div class="form-wrapper">
        <p class="info">
            <?php echo t('site', 'Si prega di inserire le informazioni dettagliate in corso e 
            informazioni precise sulla tua azienda e dei principali prodotti e servizi 
            (I campi con <span class="red">*</span> sono obbligatori):') ?>
        </p>
        <?php
        $this->widget('cms.extensions.tooltipster.tooltipster', array(
            'identifier' => '.t-bottom-left',
            'options' => array(
                'position' => 'bottom-left',
                'trigger' => 'click',
                'speed' => '150',
                'theme' => '.tooltipster-yellow',
                ))
        );
        ?>
        <?php
        $this->widget('cms.extensions.tooltipster.tooltipster', array(
            'identifier' => '.t-left',
            'options' => array(
                'position' => 'right',
                'trigger' => 'click',
                'speed' => '150',
                'theme' => '.tooltipster-yellow'
                ))
        );
        ?>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'company-shop-form',
            'htmlOptions' => array('autocomplete' => 'off', 'class' => 'shop-form', 'enctype' => 'multipart/form-data'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'validateOnType' => false,
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
            ),
                ));
        ?>
        <div class="box-form">
            <div class="row clearfix first">
                <label for="uploadimg"><?php echo t('site', 'Logo impressa:'); ?></label>
                <div class="upload-box">
                    <?php if ($shop->logo != null && $shop->logo != ''): ?>
                        <div class="thumbnail">
                            <img src="<?php echo IMAGES_URL . '/img80/' . $shop->logo; ?>"/>
                            <?php
                            echo CHtml::ajaxLink(
                                    '<span class="inner"><span class="text">
                          <i class="icon-trash icon-grey"></i>' .
                                    t('site', 'Cancela') .
                                    '</span></span>', app()->createUrl('company/deleteimg'), array(
                                'type' => 'POST',
                                'data' => array('id' => $shop->id, 'deleteImg' => 'true', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                                //'update' => '#product_img',
                                'success' => "function(data) {
                if(data==1){
                    window.parent.location.href = '" . $this->getController()->createUrl('page/render', array('slug' => plaintext($_GET['slug']))) . "';
                } else {
                    alert(" . t('site', '"Error while deleting the image"') . ");
                }
            }"), array(
                                'class' => 'btn-t grey',
                                'href' => 'javascript:void(0)',
                                'confirm' => t('site', 'Are you sure you want to delete this image?')));
                            ?> 
                        </div>
                    <?php else: ?>
                        <div class="upload-form">
                            <?php
                            $this->widget('cms.extensions.xupload.XUpload', array(
                                'url' => Yii::app()->createUrl("/company/upload"),
                                //our XUploadForm
                                'model' => $files,
                                //We set this for the widget to be able to target our own form
                                'htmlOptions' => array('id' => 'company-shop-form'),
                                'attribute' => 'uploadimg',
                                'multiple' => false,
                                //Note that we are using a custom view for our widget
                                //Thats becase the default widget includes the 'form' 
                                //which we don't want here
                                'formView' => 'common.blocks.company_shop.upload_form',
                                'uploadView' => 'cms.extensions.xupload.views.single.upload',
                                'downloadView' => 'cms.extensions.xupload.views.single.download',
                                'options' => array(
                                    'maxFileSize' => ConstantDefine::UPLOAD_MAX_SIZE,
                                    'minFileSize' => ConstantDefine::UPLOAD_MIN_SIZE,
                                    'autoUpload' => true,
                                    'sequentialUploads' => true,
                                    'acceptFileTypes' => "js:/(\.|\/)(jpe?g|png|gif)$/i",
                                    'completed' => 'js:function (e, data) {
                                    
                                    }',
                                    'added' => 'js:function (e, data) {
                                       $("a.tooltip").tooltipster("hide");
                                    }'
                                    ))
                            );
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row clearfix">
                <label for="services" class="required"><?php echo t('site', 'Attività:'); ?></label>
                <?php echo $form->textArea($model, 'services', array()); ?>
                <a class="tooltip t-bottom-left" title="<?php echo t('site', 'Enumerati serviciile si/sau produsele oferite<br /> de compania ta in maxim 200 caractere.');?>"></a>
                <?php echo $form->error($model, 'services'); ?>
            </div>
            <div class="row clearfix">
                <label for="description" class="required"><?php echo t('site', 'Descrizione:'); ?></label>
                <?php echo $form->textArea($model, 'description', array('rows' => 6)); ?>
                <?php echo $form->error($model, 'description'); ?>
            </div>
            <div class="row clearfix">
                <label for="selected_cats" class="required"><?php echo t('site', 'Settori:'); ?></label>
                <?php echo $form->hiddenField($model, 'selected_cats', array('type' => "hidden")); ?>
                <?php
                echo Chtml::listBox('listboxSelected', array(), $user->getSelectedCategories(false), array('size' => 5, 'multiple' => 'multiple', 'class' => 'listBox')
                );
                ?>
                <div class="actions">
                    <?php
                    echo CHtml::button(t('site', 'Scegliere'), array(
                        'class' => 'btn-i-s i-grey',
                        'name' => 'addCategory',
                        'onclick' => "popWindow('" . Yii::app()->controller->createUrl('render', array('slug' => 'company-shop-categories')) . "','_blank',1000,630,1,1)"
                    ));
                    ?>  

                    <?php echo CHtml::button(t('site', 'Cancela'), array('id' => 'delete_cat_copy', 'class' => 'btn-i-s i-grey')); ?>
                </div>
                <a class="tooltip t-bottom-left" title="<?php echo t('site', 'Enumerati serviciile si/sau produsele oferite<br /> de compania ta in maxim 200 caractere.');?>"></a>
                <?php echo $form->error($model, 'selected_cats'); ?>
            </div>
            <div class="row">
                <label for="marketplace"><?php echo t('site', 'Rapporti commerciali:'); ?></label>
                <?php
                echo $form->dropDownList($model, 'marketplace', array(
                    '1' => t('site', 'Italy'),
                    '2' => t('site', 'Vest Europe'),
                    '3' => t('site', 'East/Central Europe'),
                    '4' => t('site', 'Africa'),
                    '5' => t('site', 'North America'),
                    '6' => t('site', 'Sud America'),
                    '7' => t('site', 'Asia'),
                    '7' => t('site', 'Oceania'),
                        ), array('empty' => t('site', '- Seleziona -')));
                ?>
                <?php echo $form->error($model, 'marketplace'); ?>
            </div>
            <div class="row clearfix">
                <label for="certificate"><?php echo t('site', 'Certificati:'); ?></label>
                <?php echo $form->textField($model, 'certificate', array()); ?>
                <?php echo $form->error($model, 'certificate'); ?>
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
Yii::app()->clientScript->registerScript('selectList', '
      $("#delete_cat_copy").live("click",function() {
            var selected = $("#listboxSelected option:selected");
            selected.remove();
            var list =[];
            
            $("#listboxSelected option").each(function (index) {
                list.push(this.value);
            });
            $("#UserCompanyshopForm_selected_cats").val(list);
            return false;
        });
        $("#company-shop-form").submit(function() {
            var list = [];
            $("#listboxSelected").find("option").each(function() {
                list.push(this.value);
            });
            $("#UserCompanyshopForm_selected_cats").val(list);
        });
', CClientScript::POS_READY);
?>

