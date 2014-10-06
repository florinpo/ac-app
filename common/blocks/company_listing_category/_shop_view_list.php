
<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<?php
$class = ($index % 2 == 0) ? "first" : "";
$class .= ($data->company->has_membership ==1) ? " premium" : "";
?>
<li class="clearfix <?php echo $class; ?>">
<article>
    <div class="thumbnail"">
         <a href="#"><?php echo $data->selectedImage(180); ?></a>
    </div>
    <div class="data-wrap">
        <div class="header clearfix">
            <h2>
                <?php echo Chtml::link($data->company->cprofile->companyname, array('site/store', 'username' => $data->company->username, 'slug' => 'store-view')); ?>
                <?php if ($data->company->has_membership): ?>
                    <?php echo CHtml::link(t('site', 'Membro Premium'), 'javascript:void(0)', array('class' => 'premium')) ?>
                <?php endif; ?>
            </h2>
        </div>

        <div class="rating-avg clearfix">
            <?php
            $this->widget('CStarRating', array(
                'name' => 'rating' . $data->id, // an unique name
                'allowEmpty' => false,
                'value' => round($data->averageRating, 0),
                'readOnly' => true,
                'cssFile' => $layout_asset . "/css/jquery.rating.css",
                'minRating' => 1,
                'maxRating' => 5,
                'ratingStepSize' => 1,
                'starCount' => 5,
                'htmlOptions' => array('class' => 'rSmall')
            ));
            ?>
        </div>
        <div class="description"><?php echo truncate($data->description, $this->wordsLimit); ?></div>

    </div>
    <div class="actions">
        <span class="offers-num"><?php echo "<strong>" . count($data->products) . "</strong>" . t('site', ' offerte attive'); ?></span>
        <!-- begin item-selectbox-form -->
        <?php echo CHtml::beginForm('', 'post', array('id' => 'item-selectbox-form-' . $data->company->user_id, 'class' => 'item-selectbox-form')); ?>
        <?php
        echo CHtml::dropDownList('item-options', '', array(1 => t('site', 'Adauga la favorite'), 2 => t('site', 'Vezi detalii'), 3 => t('site', 'Manda messagio')), array(
            'prompt' => 'Select option', 'id' => 'item-options'));
        ?>
        <?php echo CHtml::endForm(); ?>
        <!-- end item-selectbox-form -->
    </div>
</article>
</li>