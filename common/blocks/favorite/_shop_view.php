
<?php
$class = ($index % 2 == 0) ? "first" : "last";
$class .= ($data->company->has_membership) ? " premium" : "";
?>
<li class="clearfix <?php echo $class; ?>">
<article>
    <div class="delete-fav-wrap">
        <?php
        echo CHtml::ajaxLink(t('site', 'Cancela dai preferiti'), app()->createUrl('store/favoritedelete'), array(
            'type' => 'POST',
            'dataType' =>'json',
            'data' => array('id' => $data->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
            'success' => "function(data) {
                        if(data.success==1){
                            $.fn.yiiListView.update('items-favorite-shops');
                        } else {
                            alert(" . t('site', '"Error while deleting favorite item"') . ");
                        }
                    }"), array(
            'class' => 'delete-fav-btn',
            'id' => 'delete-fav-' . $data->id,
            'href' => 'javascript:void(0)'));
        ?> 
    </div>
    <div class="thumbnail"">
         <a href="#"><?php echo $data->selectedImage(100); ?></a>
    </div>
    <div class="data-wrap">
        <h2>
            <?php echo Chtml::link($data->company->cprofile->companyname, array('site/store', 'username' => $data->company->username, 'slug' => 'store-view')); ?>
            <?php if ($data->company->has_membership): ?>
                <?php echo CHtml::link(t('site', 'Membro Premium'), 'javascript:void(0)', array('class' => 'premium')) ?>
            <?php endif; ?>
        </h2>
        <div class="bottom-bar clearfix">
            <div class="options">
                <span class="offers-num"><?php echo "<strong>" . count($data->products) . "</strong>" . t('site', ' offerte'); ?></span>
            </div>
            <div class="action-wrap">
                <?php
                echo CHtml::link('<span class="inner"><span class="text">' .
                        '<span class="icon-arrow-20 icon-white"></span>
                     </span></span>', array(
                    'site/store',
                    'username' => $data->company->username,
                    'slug' => 'store-view'
                        ), array('class' => 'btn-n green-l', 'id' => 'btn-view'));
                ?>
            </div>
        </div>

    </div>

</article>
</li>