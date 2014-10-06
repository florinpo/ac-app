<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
$default = $layout_asset . '/images/entries/avatar/180/empty.png';
?>
<section id="user-profile" class="box_round_c grid_19 omega">
    <h1 class="dash"><?php echo t('site', 'Dati conto'); ?></h1>

    <div class="form-wrapper">
        <p class="info"><?php echo t('site', 'Modifica profilo utente. Sulla base sottostante verrai contattato da potenziali clienti (I campi con <span class="red">*</span> sono obbligatori):') ?></p>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'user-profile-form',
            'htmlOptions' => array('autocomplete' => 'off', 'class' => 'user-profile-form', 'enctype' => 'multipart/form-data'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'validateOnType' => false,
                )));
        ?>
        <br />
        <div class="grid_12">
            <div class="row">
                <label for="firstname" class="required"><?php echo t('site', 'Nome:'); ?></label>
                <?php echo $form->textField($model, 'firstname', array('size' => 30, 'class' => 'userform', 'autoComplete' => 'off')); ?>
                <?php echo $form->error($model, 'firstname'); ?> 
            </div>

            <div class="row">
                <label for="lastname" class="required"><?php echo t('site', 'Cognome:'); ?></label>
                <?php echo $form->textField($model, 'lastname', array('size' => 30, 'class' => 'userform', 'autoComplete' => 'off')); ?>
                <?php echo $form->error($model, 'lastname'); ?>
            </div>


            <div class="row">
                <label for="gender" class="required"><?php echo t('site', 'Sesso:'); ?></label>
                <?php echo $form->radioButtonList($model, 'gender', array( 'female' => t('site', 'Donna'), 'male' => t('site', 'Uomo')), array('template' => '<span class="rb">{input}{label}</span>', 'separator' => '')); ?>
                <span class="clear"></span>
                <?php echo $form->error($model, 'gender'); ?>
            </div>


            <div class="row">
                <label for="birthday" class="required"><?php echo t('site', 'Data di nascita:'); ?></label>
                <?php
                $this->widget('CMaskedTextField', array(
                    'model' => $model,
                    'attribute' => 'birthday',
                    'mask' => '99/99/9999',
                    'htmlOptions' => array('size' => 20, 'placeholder' => 'dd/mm/aaaa')
                ));
                ?>
                <?php echo $form->error($model, 'birthday'); ?>     
            </div>

            <div class="row">
                <label for="region_id" class="required"><?php echo t('site', 'Regione:'); ?></label>    
                <?php
                echo $form->dropDownList($model, 'region_id', Province::getRegion(), array(
                    'ajax' => array(
                        'type' => 'POST', //request type
                        'url' => app()->createUrl('site/provincefromregion'),
                        'data' => array('region_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                        'update' => '#' . CHtml::activeId($model, 'province_id'),
                        ))
                );
                ?>
                <?php echo $form->error($model, 'region_id'); ?>
            </div>

            <?php $get_province_id = isset($_GET['province_id']) ? (int) ($_GET['province_id']) : null ?>
        <?php if ($get_province_id === null) : ?>
            <div class="row clearfix">
                <label for="province_id" class="required"><?php echo t('site', 'Provincia:'); ?></label>	 
                <?php echo $form->dropDownList($model, 'province_id', Province::getProvinceFromRegion($model->region_id, false), array('empty' => t('site', '--Select province--'), 'options' => array($model->province_id => array('selected' => true)))); ?>
                <?php echo $form->error($model, 'province_id'); ?>
            </div>
        <?php else : ?>
            <?php echo $form->hiddenField($model, 'province_id', array('value' => $get_province_id)); ?>
        <?php endif; ?>

            <div class="row">
                <label for="location"><?php echo t('site', 'Comune:'); ?></label>
                <?php echo $form->textField($model, 'location'); ?>
                <?php echo $form->error($model, 'location'); ?>
            </div>
            <div class="row">
                <label for="phone"><?php echo t('site', 'Celulare:'); ?></label>
                <?php echo $form->textField($model, 'phone'); ?>
                <?php echo $form->error($model, 'phone'); ?>
            </div>

        </div>

        <!-- sidebar -->
        <div class="grid_6 omega">
            <div class="upload-box">
                <div id="is_uploaded" class="upload-form <?php echo $profile->avatar != null && $profile->avatar != '' ? 'visible' : 'hidden' ?>">
                    <div class="thumbnail">
                        <?php echo $profile->selectedImage(180); ?>
                    </div>
                    <?php
                    echo CHtml::ajaxLink(
                            '<span class="inner"><span class="text">
                                    <i class="icon-trash icon-grey"></i>' .
                            t('site', 'Cancela imagine') .
                            '</span></span>', app()->createUrl('site/deleteavatar'), array(
                        'type' => 'POST',
                        'data' => array('id' => $profile->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                        //'update' => '#product_img',
                        'success' => "function(data) {
                                    if(data==1){
                                         $('#is_uploaded').removeClass('visible').addClass('hidden');
                                         $('#not_uploaded').removeClass('hidden').addClass('visible');
                                         $('#not_uploaded .thumbnail img').remove();
                                         $('#not_uploaded .thumbnail').html('" . $profile->selectedImage(180) . "');
                                         
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
                <div id="not_uploaded" class="upload-form <?php echo $profile->avatar == null && $profile->avatar == '' ? 'visible' :'hidden'  ?>">
                    <div id="thumbnail" class="thumbnail">
                        <?php echo $profile->selectedImage(180); ?>
                    </div>
                    <?php
                    $this->widget('cms.extensions.xupload.XUpload', array(
                        'url' => Yii::app()->createUrl("/site/uploadavatar"),
                        //our XUploadForm
                        'model' => $files,
                        //We set this for the widget to be able to target our own form
                        'htmlOptions' => array('id' => 'user-profile-form'),
                        'attribute' => 'avatar',
                        'multiple' => false,
                        //Note that we are using a custom view for our widget
                        //Thats becase the default widget includes the 'form' 
                        //which we don't want here
                        'formView' => 'common.blocks.user_account.upload_form',
                        'uploadView' => 'cms.extensions.xupload.views.single.upload_avatar',
                        'downloadView' => 'cms.extensions.xupload.views.single.download_avatar',
                        'options' => array(
                            //'maxFileSize' => ConstantDefine::UPLOAD_MAX_SIZE,
                            'minFileSize' => ConstantDefine::UPLOAD_MIN_SIZE,
                            'previewMaxWidth' => 120,
                            'previewMaxHeight' => 120,
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
        <div class="clear"></div>
        <div class="box-form-a">
            <div class="actions">
                <?php echo CHtml::submitButton(t('site', 'Salva'), array('class' => 'btn-i-n i-green')); ?>
            </div>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</section>
