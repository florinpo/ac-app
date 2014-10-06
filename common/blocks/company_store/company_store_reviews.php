<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/plugins/jquery.infieldlabel.min.js", CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/plugins/jquery.CharacterCounter.min.js", CClientScript::POS_HEAD);
?>

<?php
Yii::app()->clientScript->registerScript('reviewForm', '
//$(".labelIn label").inFieldLabels({fadeDuration:80});

$("#ShopReviewForm_comment").characterCounter({
        maximumCharacters: 800,
        minimumCharacters: 100,
        shortFormat: true,
        charactersLabel: "' . t("site", " caratteri") . '",
        shortFormatSeparator: " / ",
        positionBefore: false,
        chopText: true
});

 $("#SortForm_option").change(function(){
    $.fn.yiiListView.update("items-shop-reviews", {
        data: $(this).serialize()
    });
    jQuery.ias({
        "history":false,
        "trigger":"' . t('site', 'Mostra tutto') . '",
        "container":"#items-shop-reviews > .items",
        "item":".row",
        "pagination":"#items-shop-reviews .pager",
        "next":"#items-shop-reviews .next:not(.disabled):not(.hidden) a",
        "loader":"<img src=\"/gxc2/web/assets/bf325b0/images/loader.gif\"/>",
        "onRenderComplete":function(items) {
          $("#items-shop-reviews span[id^=\'rating\'] > input").rating({\'required\':false, \'readOnly\':true});
        },
    });
    return false;
});
    
', CClientScript::POS_READY);
?>

<div class="grid_15 box_shop_content">
    <?php
    $this->widget('zii.widgets.CMenu', array(
        'id' => 'tabnav-shop',
        'items' => $this->menu
    ));
    ?>
    <div class="tabnav-shop-body">
        <?php
        $reviews = ShopReview::model()->findAll(array('condition' => 'shop_id=:shopId', 'params' => array(':shopId' => $shop->id)));
        $usersReview = array();
        foreach ($reviews as $review) {
            $usersReview[] = $review->user->user_id;
        }
        ?>
        <?php if (!in_array(user()->id, $usersReview)): ?>
            <div id="review-box" class="clearfix">
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'review-form',
                    'htmlOptions' => array('autocomplete' => 'off', 'class' => 'review-form'),
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
                                        "url":"' . CHtml::normalizeUrl(array("store/reviewadd")) . '",
                                        "data":form.serialize(),
                                        "success":function(data){
                                           if(data.success===1){
                                               $("#review-box .review-form").hide();
                                               var confirmText = "' . t("site", "La sua recensione e state inviata con successo...") . '";
                                               $("<div class=\'success\'>").appendTo("div#review-box").hide().html(confirmText).fadeIn();
                                           }   
                                        },
                                 });
                         }
                 }'
                        )));
                ?>
                <?php echo $form->errorSummary($model, '', ''); ?>
                <?php
                echo $form->hiddenField($model, 'shop_id', array('type' => 'hidden', 'value' => $shop->id));
                ?>
                <div class="row clearfix rating">
                    <div class="vote-box">
                        <?php
                        $this->widget('CStarRating', array(
                            'attribute' => 'rating',
                            'model' => $model,
                            'titles' => array(1 => 'Foarte slab', 2 => 'Slab', 3 => 'Bun', 4 => 'Foarte bun', 5 => 'Excelent'),
                            'allowEmpty' => false,
                            'readOnly' => false,
                            'cssFile' => $layout_asset . "/css/jquery.rating.css",
                            'minRating' => 1,
                            'maxRating' => 5,
                            'ratingStepSize' => 1,
                            'focus' => "function(value, link){
                            var tip = $('#vote-output span');
                            tip[0].data = tip[0].data || tip.html();
                            tip.html(link.title || 'value: '+value);
                        }",
                            'blur' => "function(value, link){
                            var tip = $('#vote-output span');
                            $('#vote-output span').html(tip[0].data || '');
                        }",
                            'callback' => "function(value, link){
                            var tip = $('#vote-output span');
                            tip[0].data = tip.html();
                            tip.html(link.title);
                        }",
                        ));
                        ?>
                    </div>
                    <div id="vote-output">
                        <span class="text"><?php echo t('site', 'Vota qui!') ?></span>
                    </div>
                    <?php echo $form->error($model, 'rating'); ?>

                </div>
                <div class="row clearfix">

                    <?php if (!user()->isGuest): ?>
                        <div class="labelIn">
                            <label for="ShopReviewForm_comment"><?php echo t('site', 'Aggiungi la tua recensione'); ?></label>
                            <?php echo $form->textArea($model, 'comment', array('rows' => 3, 'cols' => 95)); ?>
                        </div>
                    <?php else: ?>
                        <div class="is-guest">
                            <div class="text">
                                <?php
                                echo t('site', 'Per scrivere la tua recensione devi essere conneso al tuo conto AffariClub. <br /> :accedi il tuo conto o pure Registrati', array(
                                    ':accedi' => CHtml::link(t('site', 'Accedi'), app()->createUrl('page/render', array('slug' => 'sign-in')))
                                ));
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php echo $form->error($model, 'comment'); ?>
                    <?php if (!user()->isGuest): ?>
                        <?php echo CHtml::submitButton(t('site', 'Publica'), array('class' => 'btn-i-n i-green')); ?>
                    <?php else: ?>
                        <?php echo CHtml::link(t('site', 'Publica'), app()->createUrl('page/render', array('slug' => 'sign-in')), array('class' => 'btn-i-n i-green')); ?>
                    <?php endif; ?>
                </div>

                <?php $this->endWidget(); ?>
            </div>
        <?php else: ?>
            <div class="notification-box notification-box-info">
                <?php echo t('site', 'Hai gia scritto una recensione su questo negozio.') ?>
            </div>
        <?php endif; ?>


        <div id="reviews_results">
            <div class="header clearfix">
                <div class="summary"><?php echo t('site', ':total recensioni', array(':total' => $total)); ?></div>
                <div class="sorter-wrap">
                    <?php
                    echo CHtml::form('', 'get', array('class' => 'sorter-form'));
                    echo CHtml::label(t('site', 'Ordina per'), 'SortForm[option]');
                    echo CHtml::dropDownList('SortForm[option]', $current, $this->getStringStype(), array());
                    echo CHtml::endForm();
                    ?>
                </div>
            </div>
            <?php
            $this->render('common.blocks.company_store._list_reviews', array('dataProvider' => $dataProvider, 'layout_asset' => $layout_asset));
            ?>
        </div>
    </div>
</div>

