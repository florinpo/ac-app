<article>
    <div class="thumbnail"">
         <a href="#"><?php echo $shop->selectedImage(100); ?></a>
    </div>
    <div class="data-wrap">
        <h2>
            <?php echo Chtml::link($cprofile->companyname, array('site/store', 'username' => $cuser, 'slug' => 'store-view')); ?>
            <?php if ($cmembership == 1): ?>
                <?php echo CHtml::link(t('site', 'Membro Premium'), 'javascript:void(0)', array('class' => 'premium')) ?>
            <?php endif; ?>
        </h2>
        <div class="rating-avg clearfix">
            <?php
            $this->widget('CStarRating', array(
                'name' => 'rating' . $shop->id, // an unique name
                'allowEmpty' => false,
                'value' => round($shop->averageRating, 0),
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
        <div class="bottom-bar clearfix">
            <div class="options">
                <span class="offers-num"><?php echo "<strong>" . count($shop->products) . "</strong>" . t('site', ' offerte'); ?></span>
            </div>
            <div class="action-wrap">
                <!-- begin item-selectbox-form -->
                <?php echo CHtml::beginForm('', 'post', array('id' => 'item-selectbox-form-' . $shop->id, 'class' => 'item-selectbox-form')); ?>
                <?php
                echo CHtml::dropDownList('item-options', '', array(1 => t('site', 'Adauga la favorite'), 2 => t('site', 'Vezi detalii'), 3 => t('site', 'Manda messagio')), array(
                    'prompt' => 'Select option', 'id' => 'item-options'));
                ?>
                <?php echo CHtml::endForm(); ?>
            </div>
        </div>
    </div>

</article>