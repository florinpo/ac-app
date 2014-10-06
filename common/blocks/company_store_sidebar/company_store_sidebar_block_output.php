<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<div class="box_shop_sidebar grid_9">
    <div class="row first clearfix">
        <div class="grid_4 alpha">
            <div class="cover img_rounded">
                <?php echo $shop->selectedImage(180, 'frontend'); ?>
            </div>
        </div>
        <div class="data grid_5 alpha">
            <h1 class="cname"><?php echo $company->cprofile->companyname; ?></h1>
            <?php echo CHtml::link(t('site', 'Membro Premium'), '#', array('class' => 'premium-l')) ?>
            <div class="rating-avg">
                <?php 
                $this->widget('CStarRating', array(
                    'name'=>'average-rating',
                    'allowEmpty' => false,
                    'value' => round($averageRating,0),
                    'readOnly' => true,
                    'cssFile' => $layout_asset . "/css/jquery.rating.css",
                    'minRating' => 1,
                    'maxRating' => 5,
                    'ratingStepSize' => 1,
                    'starCount'=>5,
                    'htmlOptions' => array('class'=>'rSmall')
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="actions clearfix">
            <div id="s_not_favorite" class="<?php echo!empty($favShop) ? 'hidden' : 'active'; ?>">
                <?php
                if ($countFavUsers > 0) {
                    $text = '<span class="counter">' . $countFavUsers . '</span>';
                    $text .= t('site', ' fani, diventa anche tu');
                } else {
                    $text = '<span class="counter"></span>';
                    $text = t('site', 'Aggiungi ai preferiti');
                }

                echo CHtml::ajaxLink('<span class="inner"><span class="text">' .
                        $text .
                        '<span class="icon"></span></span>', app()->createUrl('store/favoriteadd'), array(
                    'type' => 'POST',
                    'dataType' => 'json',
                    'data' => array('id' => $shop->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                    'beforeSend' => 'function(){
                         $("#s_not_favorite .loader").show();
                    }',
                    'complete' => 'function(){
                         $("#s_not_favorite .loader").hide();
                    }',
                    'success' => "function(data) {
                        if(data.success==1) {
                            $('#favorite-delete-store .counter').html(data.count);
                            $('#s_not_favorite').removeClass('active').addClass('hidden');
                            $('#s_is_favorite').removeClass('hidden').addClass('active');
                        } else {
                            alert(" . t('site', '"Error! The shop cannot be added as favorite."') . ");
                        }
                    }"), array(
                    'id' => 'favorite-add-store',
                    'class' => 'btn-s green-l3 favorite-btn add',
                    'href' => 'javascript:void(0)'));
                ?>
                <div class="loader"></div>
            </div>
            <div id="s_is_favorite" class="<?php echo empty($favShop) ? 'hidden' : 'active'; ?>">
                <?php
                echo CHtml::ajaxLink('<span class="inner"><span class="text">' .
                        '<span class="counter">' . $countFavUsers . '</span>' .
                        t('site', ' fani, cancela dai preferiti') .
                        '<span class="icon"></span></span>', app()->createUrl('store/favoritedelete'), array(
                    'type' => 'POST',
                    'dataType' => 'json',
                    'data' => array('id' => $shop->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                    'beforeSend' => 'function(){
                        $("#s_is_favorite .loader").show();
                    }',
                    'complete' => 'function(){
                        $("#s_is_favorite .loader").hide();
                    }',
                    'success' => "function(data) {
                        if(data.success==1) {
                            if(data.count>0){
                              $('#favorite-add-store .counter').html(data.count);
                            } else {
                              $('#favorite-add-store .counter').text('');
                              $('#favorite-add-store .text').html(" . t('site', '"Aggiungi ai preferiti"')."+'<span class=\"icon\"></span>');
                            }
                            
                            $('#s_not_favorite').removeClass('hidden').addClass('active');
                            $('#s_is_favorite').removeClass('active').addClass('hidden');
                        } else {
                            alert(" . t('site', '"Error while deleting favorite shop."') . ");
                        }
                    }"), array(
                    'id' => 'favorite-delete-store',
                    'class' => 'btn-s green-l3 favorite-btn delete',
                    'href' => 'javascript:void(0)'));
                ?>
                <div class="loader"></div>
            </div>
            <?php echo CHtml::link('<span class="inner"><span class="text">' .
                        '<span class="counter">' . $shop->countReviews . '</span>' .
                        t('site', ' recesioni, scrivi anche tu') .
                        '<span class="icon"></span></span>', 
                        app()->createUrl('site/store', array('username' => $company->username, 'shop_page' => 'recensioni')), 
                       array('class' => 'btn-s green reviews-btn'));
            ?>
        </div>

    </div>
<?php if (!empty($shop->sections)): ?>
        <div class="row clearfix">
            <h3 class="h-icon"><?php echo t('site', 'Prodotti'); ?></h3>

            <ul class="p-sections-list">
    <?php foreach ($shop->sections as $k => $section): ?>
                    <?php if (count($shop->sections) == ($k + 1)): ?>
                        <li class="last">
                        <?php echo CHtml::link($shop->sections[$k]->name, '#'); ?>
                        </li>
                        <?php else: ?>
                        <li>
                        <?php echo CHtml::link($shop->sections[$k]->name, '#'); ?>
                        </li>
                        <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
<?php endif; ?>

    <div class="row last clearfix">
        <h3 class="h-icon"><?php echo t('site', 'Trovata nel'); ?></h3>
<?php $categories = $shop->categories; ?>
        <?php if (count($categories) > 0): ?>
            <ul class="breads-categories">
            <?php
            foreach ($categories as $cat) {
                echo "<li>";
                echo $this->getCategoryParents($cat->id) . '<br />';
                echo "</li>";
            }
            ?>
            </ul>
            <?php endif; ?>
    </div>

</div>

