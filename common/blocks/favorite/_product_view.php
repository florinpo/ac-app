<?php
   $class = ($index % 2 == 0) ? "first" : "last";
   $class .= ($data->shop->company->has_membership) ? " premium" : "";
?>
<li class="clearfix <?php echo $class; ?>">
<article>
    <?php if ($data->shop->company->has_membership): ?>
        <div class="premium-wrap">
            <?php echo CHtml::link(t('site', 'Membro Premium'), 'javascript:void(0)', array('class' => 'premium')) ?>
        </div>
        <div class="delete-fav-wrap">
            <?php
                echo CHtml::ajaxLink(t('site', 'Cancela dai preferiti'), app()->createUrl('productsale/favoritedelete'), array(
                'type' => 'POST',
                'dataType' =>'json',
                'data' => array('id' => $data->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                'success' => "function(data) {
                         if(data.success==1){
                            $.fn.yiiListView.update('items-favorite-products');
                        } else {
                            alert(" . t('site', '"Error while deleting favorite item"') . ");
                        }
                    }"), array(
                'class' => 'delete-fav-btn',
                'id'=>'delete-fav-'.$data->id,
                'href' => 'javascript:void(0)'));
            ?> 
        </div>
    <?php endif; ?>
    <div class="thumbnail">
        <a href="#"><?php echo $data->selectedImage(180); ?></a>
    </div>
    <div class="data-wrap">
        <h2><?php echo CHtml::link($data->name, '#'); ?></h2>
    </div>
    <div class="bottom-bar clearfix">
        <div class="price">
            <?php if ($data->price > 0): ?>
                <div class="old-p">
                    <span class="data"><?php echo '500.00'; ?></span>
                    <span class="currency">&euro;</span>
                </div>
                <div class="regular">
                    <span class="data"><?php echo $data->price; ?></span>
                    <span class="currency">&euro;</span>
                </div>
            <?php else: ?>
                <span class="data-empty"><?php echo t('site', '') ?></span>
            <?php endif; ?>
        </div>
        <div class="action-wrap">
        <?php
        echo CHtml::link('<span class="inner"><span class="text">' .
               
                '<span class="icon-arrow-20 icon-white"></span>
                     </span></span>', array(
            'site/store',
            'username' => $data->shop->company->username,
            'page_slug' => 'vendita',
            'prod_id' => $data->id,
            'prod_slug' => $data->slug), array('class' => 'btn-n green-l', 'id'=>'btn-view'));
        ?>
        </div>
    </div>
</article>
</li>