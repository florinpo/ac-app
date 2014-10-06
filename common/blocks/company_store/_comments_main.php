<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/jquery.infieldlabel.min.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/jquery.CharacterCounter.min.js", CClientScript::POS_HEAD);
?>

<?php
Yii::app()->clientScript->registerScript('commentForm', '
    
$(".labelIn label").inFieldLabels({fadeDuration:80});

$("#ProductSaleCommentForm_comment").characterCounter({
        maximumCharacters: 500,
        shortFormat: true,
        charactersLabel: "' . t("site", " caratteri rimanenti") . '",
        shortFormatSeparator: "",
        positionBefore: false,
        chopText: true
    });
// $("#SortForm_option").change(function(){
//    $.fn.yiiListView.update("items-shop-reviews", {
//        data: $(this).serialize()
//    });
//    jQuery.ias({
//        "history":false,
//        "trigger":"' . t('site', 'Mostra tutto') . '",
//        "container":"#items-shop-reviews > .items",
//        "item":".row",
//        "pagination":"#items-shop-reviews .pager",
//        "next":"#items-shop-reviews .next:not(.disabled):not(.hidden) a",
//        "loader":"<img src=\"/gxc2/web/assets/bf325b0/images/loader.gif\"/>",
//        "onRenderComplete":function(items) {
//          $("#items-shop-reviews span[id^=\'rating\'] > input").rating({\'required\':false, \'readOnly\':true});
//        },
//    });
//    return false;
//});
    
', CClientScript::POS_READY);
?>

<div id="comment-box" class="clearfix">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'comment-form',
        'htmlOptions' => array('autocomplete' => 'off', 'class' => 'comment-form'),
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'hideErrorMessage' => true,
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
            'afterValidate' => 'js:function(form,data,hasError){
                        if(!hasError){
                                $.ajax({
                                        "type":"POST",
                                        "dataType":"json",
                                        "url":"' . CHtml::normalizeUrl(array("productsale/commentadd")) . '",
                                        "data":form.serialize(),
                                        "success":function(data){
                                           if(data.success===1){
                                           alert(true);
//                                               $("#review-box .review-form").hide();
//                                               var confirmText = "' . t("site", "La sua recensione e state inviata con successo...") . '";
//                                               $("<div class=\'success\'>").appendTo("div#review-box").hide().html(confirmText).fadeIn();
                                           }   
                                        },
                                 });
                         }
                 }'
            )));
    ?>
    <?php echo $form->errorSummary($model, '', ''); ?>
    <?php
    echo $form->hiddenField($model, 'product_id', array('type' => 'hidden', 'value' => $product->id));
    ?>
    <div class="row clearfix">
        <div class="labelIn">
            <label for="ProductSaleCommentForm_comment"><?php echo t('site', 'Aggiungi il tuo commento'); ?></label>
            <?php echo $form->textArea($model, 'comment', array('rows' => 2, 'cols' => 88)); ?>
        </div>
        <?php echo $form->error($model, 'comment'); ?>
        <?php if (!user()->isGuest): ?>
            <?php echo CHtml::submitButton(t('site', 'Publica'), array('class' => 'btn-i-n i-dgrey')); ?>
        <?php else: ?>
            <?php echo CHtml::link(t('site', 'Publica'), app()->createUrl('page/render', array('slug' => 'sign-in')), array('class' => 'btn-i-n i-dgrey')); ?>
        <?php endif; ?>
    </div>
    <?php $this->endWidget(); ?>
</div>


