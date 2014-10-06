<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
$default = $layout_asset . '/images/entries/shops/180/empty.png';
?>
<?php
$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.t-bottom-left',
    'options' => array(
        'position' => 'bottom-left',
        'trigger' => 'hover',
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
        'trigger' => 'hover',
        'speed' => '150',
        'theme' => '.tooltipster-yellow'
        ))
);
?>
<section id="manage-store">
    <div class="box_round_c grid_19 omega">
        <h1 class="dash"><?php echo t('site', 'Gestisci negozio') ?></h1>
        <?php
        $this->widget('zii.widgets.CMenu', array(
            'id' => 'tabnav',
            'items' => array(
                array(
                    'label' => t('site', 'Modifica negozio'),
                    'url' => array('page/render', 'slug' => 'manage-store', 'op' => 'info-negozio'),
                    'active' =>!isset($_GET['op']) || $_GET['op'] == 'info-negozio' ? true : false
                ),
                array(
                    'label' => t('site', 'Aggiungi sezioni'),
                    'url' => array('page/render', 'slug' => 'manage-store', 'op' => 'sezioni-negozio'),
                    'active' =>isset($_GET['op']) && $_GET['op'] == 'sezioni-negozio' ? true : false
                )
            )
        ));
        ?>
        <div class="tabnav-body">
            <div class="form-wrapper">
                <p class="info">
                    <?php echo t('site', 'Si prega di inserire le informazioni dettagliate in corso e 
            informazioni precise sulla tua azienda e dei principali prodotti e servizi 
            (I campi con <span class="red">*</span> sono obbligatori):') ?>
                </p>
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
                            <div id="is_uploaded" class="upload-form <?php echo $shop->logo != null && $shop->logo != '' ? 'visible' : 'hidden' ?>">
                                <div class="thumbnail">
                                    <?php echo $shop->selectedImage(180); ?>
                                </div>
                                <?php
                                echo CHtml::ajaxLink(
                                        '<span class="inner"><span class="text">
                                    <i class="icon-trash icon-grey"></i>' .
                                        t('site', 'Cancela logo') .
                                        '</span></span>', app()->createUrl('company/deletelogo'), array(
                                    'type' => 'POST',
                                    'data' => array('id' => $shop->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                                    //'update' => '#product_img',
                                    'success' => "function(data) {
                                    if(data==1){
                                         $('#is_uploaded').removeClass('visible').addClass('hidden');
                                         $('#not_uploaded').removeClass('hidden').addClass('visible');
                                    } else {
                                        alert(" . t('site', '"Error while deleting the image"') . ");
                                    }
                                }"), array(
                                    'id' => 'delete_logo',
                                    'class' => 'btn-n grey cancel',
                                    'href' => 'javascript:void(0)',
                                        //'confirm' => t('site', 'Are you sure you want to delete this image?')
                                ));
                                ?>
                            </div>

                            <div id="not_uploaded" class="upload-form <?php echo $shop->logo == null && $shop->logo == '' ? 'visible' : 'hidden' ?>">
                                <div id="thumbnail" class="thumbnail">
                                    <img src="<?php echo $default; ?>" />
                                </div>
                                <?php
                                $this->widget('cms.extensions.xupload.XUpload', array(
                                    'url' => Yii::app()->createUrl("/company/uploadlogo"),
                                    //our XUploadForm
                                    'model' => $files,
                                    //We set this for the widget to be able to target our own form
                                    'htmlOptions' => array('id' => 'company-shop-form'),
                                    'attribute' => 'uploadimg',
                                    'multiple' => false,
                                    //Note that we are using a custom view for our widget
                                    //Thats becase the default widget includes the 'form' 
                                    //which we don't want here
                                    'formView' => 'common.blocks.company_manage_store.upload_form',
                                    'uploadView' => 'cms.extensions.xupload.views.single.upload_logo',
                                    'downloadView' => 'cms.extensions.xupload.views.single.download_logo',
                                    'options' => array(
                                        'maxFileSize' => ConstantDefine::UPLOAD_MAX_SIZE,
                                        'minFileSize' => ConstantDefine::UPLOAD_MIN_SIZE,
                                        'previewMaxWidth' => 180,
                                        'previewMaxHeight' => 180,
                                        'autoUpload' => true,
                                        'sequentialUploads' => true,
                                        'acceptFileTypes' => "js:/(\.|\/)(jpe?g|png|gif)$/i",
                                        'added' => 'js:function(e, data){
                                        var that = $(this).data("fileupload");
                                        var $count = $(".template-upload .error").length;
                                        $.each(data.files, function (index, file) {
                                            if (file.error) {
                                                $(".template-upload .error").each(function(index){
                                                if($count>1 && index===0)
                                                    $(this).remove();
                                                })
                                                that._adjustMaxNumberOfFiles(1);
                                            } else {
                                               $(".template-upload .error").remove();
                                            }
                                        });
                                     }',
                                        'completed' => 'js:function(e,data){
                                         $(".template-download").css({"height":"auto"});
                                         $("#thumbnail").hide();
                                         $(".single-actionbar").hide();
                                         $(".template-upload").remove();
                                         $("#hintUpload").hide();
                                     }',
                                        'destroy' => 'js:function(e, data){
                                         var that = $(this).data("fileupload"),
                                            removeNode = function () {
                                                that._transition(data.context).done(
                                                    function () {
                                                        $(this).remove();
                                                        $(".upload-form #thumbnail").fadeIn();
                                                        $(".upload-form .single-actionbar").show();
                                                         $("#hintUpload").show();
                                                        that._trigger("destroyed", e, data);
                                                        
                                                    }
                                                );
                                            };
                                            if (data.url) {
                                                $.ajax(data).done(removeNode());
                                                that._adjustMaxNumberOfFiles(1);
                                            } else {
                                                removeNode();
                                            }
                                        
                                     }'
                                        ))
                                );
                                ?>
                                <a id="hintUpload" class="tooltip t-left" title="<?php echo t('site', 'Max 200 kb.'); ?>"></a>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">

                        <?php
                        $checked = isset($shop) && $shop->shipping_available > 0 ? 'checked' : false;
                        $classPanel = ($checked == 'checked') ? 'active' : 'hidden';
                        ?>
                        <label for="shipping_available"><?php echo t('site', 'Produse livrabile:'); ?></label>
                        <?php echo $form->checkBox($model, 'shipping_available', array('checked' => $checked)); ?>
                        <?php echo $form->error($model, 'shipping_available'); ?>
                    </div>
                    <div class="op-panel <?php echo $classPanel; ?>">
                        <div class="row clearfix">
                            <label for="selected_shipopts" class="required"><?php echo t('site', 'Expediere comenzi prin:'); ?></label>
                            <?php
                            echo $form->checkBoxList($model, 'selected_shipopts', ConstantDefine::getShippingTypes(), array(
                                'class' => 'checkbox-group', 'separator' => '', 'labelOptions' => array('style' => 'width:auto; margin-right:20px; color:#444;')
                            ));
                            ?>
                            <?php echo $form->error($model, 'selected_shipopts'); ?>
                        </div>

                        <div class="row clearfix">
                            <label for="shipping_description" class="required"><?php echo t('site', 'Detalii livrare:'); ?></label>
                            <div class="txtaread-wrap">
                                <?php echo $form->textArea($model, 'shipping_description', array('rows' => 6)); ?>
                            </div>
                            <a class="tooltip t-bottom-left" title="<?php echo t('site', 'Enumerati serviciile si/sau produsele oferite<br /> de compania ta in maxim 200 caractere.'); ?>"></a>
                            <?php echo $form->error($model, 'shipping_description'); ?>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <label for="services" class="required"><?php echo t('site', 'Attività:'); ?></label>
                        <?php echo $form->textArea($model, 'services', array()); ?>
                        <a class="tooltip t-bottom-left" title="<?php echo t('site', 'Enumerati serviciile si/sau produsele oferite<br /> de compania ta in maxim 200 caractere.'); ?>"></a>
                        <?php echo $form->error($model, 'services'); ?>
                    </div>
                    <div class="row clearfix">
                        <label for="description" class="required"><?php echo t('site', 'Descrizione:'); ?></label>
                        <div class="txtaread-wrap">
                            <?php echo $form->textArea($model, 'description', array('rows' => 6)); ?>
                        </div>
                        <?php echo $form->error($model, 'description'); ?>

                    </div>
                    <div class="row clearfix">
                        <label for="selected_cats" class="required"><?php echo t('site', 'Settori:'); ?></label>
                        <?php echo $form->hiddenField($model, 'selected_cats', array('type' => "hidden")); ?>
                        <?php
                        echo Chtml::listBox('listboxSelected', array(), $shop->getSelectedCategories(false), array('size' => 5, 'multiple' => 'multiple', 'class' => 'listBox')
                        );
                        ?>
                        <div class="actions">
                            <?php
                            echo CHtml::link('<span class="inner"><span class="text">'
                                    . t('site', 'Scegliere')
                                    . '<span class="icon-plus-circle icon-grey"></span></span></span>', 'javascript:void(0)', array(
                                'id' => 'delete_cat_copy',
                                'class' => 'btn-s grey',
                                'onclick' => "popWindow('" . Yii::app()->controller->createUrl('render', array('slug' => 'shop-categories')) . "','_blank',1000,630,1,1)"
                            ));
                            ?>
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
                        <label for="certificate"><?php echo t('site', 'Certificati:'); ?></label>
                        <?php echo $form->textField($model, 'certificate', array()); ?>
                        <?php echo $form->error($model, 'certificate'); ?>
                    </div>

                    <div class="row clearfix">
                        <label for="delivery_type"><?php echo t('site', 'Aria de prestari servicii / livrare produse:'); ?></label>
                        <?php echo $form->radioButtonList($model, 'delivery_type', ConstantDefine::getDeliverTypes(), array('class' => 'radio-group', 'separator' => '', 'labelOptions' => array('style' => 'width:auto; margin-right:20px; color:#444'))); ?>
                        <?php echo $form->error($model, 'delivery_type'); ?>
                    </div>

                    <div class="hidden" id="del-local">
                        <div class="row clearfix">
                            <label for="region_id"><?php echo t('site', 'Regione'); ?></label>    
                            <?php
                            echo $form->dropDownList($model, 'region_id', Province::getRegion(), array(
                                'ajax' => array(
                                    'type' => 'POST', //request type
                                    'url' => app()->createUrl('site/provincefromregion'),
                                    'data' => array('region_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                                    'update' => '#UserCompanyShopForm_province_id',
                                    ))
                            );
                            ?>
                            <?php echo $form->error($model, 'region_id'); ?>
                        </div>


                        <div class="row clearfix">
                            <label for="province_id" class="required"><?php echo t('site', 'Provincia'); ?></label>	 
                            <?php echo $form->dropDownList($model, 'province_id', Province::getProvinceFromRegion($model->region_id, false), array('prompt' => t('site', '--Select province--'), 'options' => array($model->province_id => array('selected' => true)))); ?>
                            <?php echo $form->error($model, 'province_id'); ?>
                        </div>

                    </div>
                    <div class="block hidden" id="del-regional" >
                        <div class="row clearfix">
                            <label for="r_region_id"><?php echo t('site', 'Regione:'); ?></label>    
                            <?php
                            echo $form->dropDownList($model, 'r_region_id', Province::getRegion(), array(
                                'ajax' => array(
                                    'type' => 'POST', //request type
                                    'url' => app()->createUrl('site/provincefromregion'),
                                    'data' => array('region_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                                    'update' => '#regional_province_id',
                                    ))
                            );
                            ?>

                        </div>

                        <div class="row clearfix">
                            <label for="r_province_id"><?php echo t('site', 'Provincia:'); ?></label>	 
                            <?php
                            echo $form->listBox($model, 'r_province_id', Province::getProvinceFromRegion($model->r_region_id, false), array(
                                'id' => 'regional_province_id',
                                'class' => 'listBox-s',
                                'size' => 6,
                                'options' => array()
                            ));
                            ?>
                            <div class="actions">

                                <?php
                                echo CHtml::ajaxLink('<span class="inner"><span class="text">'
                                        . t('site', 'Select')
                                        . '<span class="icon-arrow-down icon-grey"></span></span></span>', app()->createUrl('/site/selectedprovinces'), array(
                                    'type' => 'POST',
                                    'data' => array('prov_id' => 'js:$("#regional_province_id").val()', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                                    'success' => 'function(data) {
                                        var $response=$(data).val();
                                        var list = [];
                                        if($("#listbox_provinces option").length<5){
                                            if (!$("#listbox_provinces").find("option[value="+$response+"]").length > 0) {
                                                $("#listbox_provinces").append(data);
                                                $("#listbox_provinces option").each(function (index) {
                                                    list.push(this.value);
                                                });
                                                $("#UserCompanyShopForm_selected_provinces").val(list);
                                            };
                                        }

                                        
                                 }'
                                        ), array(
                                    'class' => 'btn-s grey'
                                ));
                                ?>
                            </div>
                        </div>


                        <div class="row selected">
                            <label for="selected_provinces" class="required"><?php echo t('site', 'Selezionate:'); ?></label>

                            <?php echo $form->hiddenField($model, 'selected_provinces', array('type' => "hidden")); ?>
                            <?php
                            echo Chtml::listBox('listbox_provinces', array(), $shop->getSelectedProvinces(false), array(
                                'multiple' => 'multiple',
                                'class' => 'listBox-s',
                                'size' => 6,
                            ));
                            ?>
                            <div class="actions">
                                <?php
                                echo CHtml::link('<span class="inner"><span class="text">'
                                        . t('site', 'Cancela')
                                        . '<span class="icon-x-circle icon-grey"></span></span></span>', '#', array('id' => 'delete_prov', 'class' => 'btn-s grey'));
                                ?>
                            </div>
                            <?php echo $form->error($model, 'selected_provinces'); ?>
                        </div>
                    </div>
                </div>
                <div class="box-form-a">
                    <div class="actions">
                        <?php echo CHtml::submitButton(t('site', 'Salva'), array('class' => 'btn-i-n i-green', 'id' => 'saveBtn')); ?>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
</section>

<?php
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/ckeditor/ckeditor.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/ckeditor/adapters/jquery.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScript('shopForm', '
    
      function sendProvIds(){ 
           var listIds =[];
           $("#regional_province_id option:selected").each(function (index) {
               listIds.push(this.value);
           });
           return listIds;
      };
      
        $("#delete_cat_copy").live("click",function() {
            var selected = $("#listboxSelected option:selected");
            selected.remove();
            var list =[];
            $("#listboxSelected option").each(function (index) {
                list.push(this.value);
            });
            $("#UserCompanyShopForm_selected_cats").val(list);
            return false;
        });
        $("#company-shop-form").submit(function() {
            var list = [];
            $("#listboxSelected").find("option").each(function() {
                list.push(this.value);
            });
            $("#UserCompanyShopForm_selected_cats").val(list);
        });
        
       //toggle items
        if($("#UserCompanyShopForm_shipping_available").is(":checked"))
        {
            $(".op-panel").removeClass("hidden").addClass("active");  // checked
        }
        else
        {
            $(".op-panel").removeClass("active").addClass("hidden");  // unchecked
        }

        $("#UserCompanyShopForm_shipping_available").click(function(){
            $(".op-panel").toggle(this.checked);
        });
        
        
       $("#delete_prov").click(function() {
            var selected = $("#listbox_provinces option:selected");
            selected.remove();
            var list =[];
            $("#listbox_provinces option").each(function (index) {
                list.push(this.value);
            });
            $("#UserCompanyShopForm_selected_provinces").val(list);
            return false;
        });
        
    $("#UserCompanyShopForm_delivery_type input:radio").click(function() {
        if ($(this).val() === "1") {
            $("#del-regional").attr("class", "hidden");
            $("#del-local").attr("class", "visible");
        } else if ($(this).val() === "2") {
            $("#del-local").attr("class", "hidden");
            $("#del-regional").attr("class", "visible");
        } else if ($(this).val() === "3") {
            $("#del-local").attr("class", "hidden");
            $("#del-regional").attr("class", "hidden");
        } 
    });

    $("#UserCompanyShopForm_delivery_type input:radio").each(function(i) {
        var input = $("#UserCompanyShopForm_delivery_type_"+i+":checked");
        if(input.val() == 1){
            $("#del-regional").attr("class", "hidden");
            $("#del-local").attr("class", "visible");
        } else if(input.val() == 2){
            $("#del-regional").attr("class", "visible");
            $("#del-local").attr("class", "hidden");
        } else if(input.val() == 3){
            $("#del-regional").attr("class", "hidden");
            $("#del-local").attr("class", "hidden");
        }
    });
    
    // CKEditor configuration
         var config = {
            height: 100,
            resize_minWidth: 430,
            resize_maxWidth: 430,
            width : 430,
            language: "it",
            toolbar :
                [
                ["Bold","BulletedList"]
            ]
        };
        // Set for the CKEditor
        $("#UserCompanyShopForm_description, #UserCompanyShopForm_shipping_description").ckeditor(config);

', CClientScript::POS_READY);
?>