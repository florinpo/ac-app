

<div class="grid_15 box_shop_content">
    <?php
    $this->widget('zii.widgets.CMenu', array(
        'id' => 'tabnav-shop',
        'items' => $this->menu
    ));
    ?>
    <div class="tabnav-shop-body clearfix">
        <article class="item-view">
            <div id="item-gallery" class="grid_7">
                <div class="img-view">
                    <?php
                    echo $product->selectedImage(180);
                    ?>
                </div>
                <div class="zoom-img">
                    <?php echo Chtml::link(t('site', 'Ingrandisci l\'imagine'), "#modal-content", array('class' => 'open_gallery')); ?>
                </div>
                <?php
                $images = $product->pimages;
                if (count($images) > 1):
                    ?>
                    <div class="gallery-thumbs">
                        <ul>
                            <?php foreach ($images as $k => $image): ?>

                                <?php
                                if ($k == 0) {
                                    echo "<li id='thumb-" . $k . "' class='first'>";
                                } else {
                                    echo "<li id='thumb-" . $k . "'>";
                                }
                                ?>

                                <?php
                                $selectedImg = $product->selectedImageObj();
                                $class = ($selectedImg->id == $image->id) ? 'current' : '';
                                $src = IMAGES_URL . "/img80/" . $image->path;
                                echo CHtml::ajaxLink(Chtml::image($src, 'thumb ' . $k, array())
                                        , app()->createUrl('store/productgallery'), array(
                                    'type' => 'POST',
                                    'datatype' => 'json',
                                    'data' => array(
                                        'selectedImage' => $images[$k]->id,
                                        'productId' => $product->id,
                                        'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                                    ),
                                    'success' => "function(data) {
                        var json = $.parseJSON(data);
                        var list = $('.gallery-thumbs ul li a');
                        list.each(function(i) {
                                $(this).removeClass('current');
                        });
                        var list2 = $('#modal-content .thumbnails_list li a');
                        list2.each(function(i) {
                                $(this).removeClass('current');
                        });
                        $(json).each(function(i,val){
                             $('#item-gallery .img-view img').attr('src', '" . IMAGES_URL . "/img180/" . "'+ val.path);
                             $('#item-gallery li#thumb-'+val.key).find('a').addClass('current');
                             $('#modal-content .img_holder img').attr('src', '" . IMAGES_URL . "/img400/" . "'+ val.path);
                             $('#modal-content .thumbnails_list li#thumb-'+val.key).find('a').addClass('current');
                        });
                        $('.gallery-thumbs ul li:first').addClass('first');
                        
            }"), array(
                                    'href' => 'javascript:void(0)',
                                    'class' => $class));
                                ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                    </div>
                <?php endif; ?>
            </div>

            <div class="entry-content grid_7">
                <h1><?php echo $product->name; ?></h1>
                <div class="item_details">
                    <div class="row">
                        <div class="data_label"><?php echo t('site', 'Modelo: '); ?></div>
                        <div class="data_name"><?php echo $product->model; ?></div>
                    </div>
                    <div class="row price">
                        <div class="data_label"><?php echo t('site', 'Prezzo: '); ?></div>
                        <div class="data_name">
                            <?php echo ($product->price > 0) ? $product->price : t('site', '270'); ?>
                            <span class="currency">&euro;</span>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <div class="row">
                        <p><?php echo t('site', 'Hai qualche domanda?') ?></p>
                        <?php
                        echo CHtml::link('<span class="inner"><span class="text">' .
                                t('site', 'Chiedere al venditore') .
                                '<span class="icon-mail icon-white"></span>
                     </span></span>', array(), array('class' => 'btn-s black'));
                        ?>
                    </div>

                    <div id="not_favorite" class="row last <?php echo!empty($favOffer) ? 'hidden' : 'active'; ?>">

                        <p><?php echo t('site', 'Vuoi rivedere il prodotto piu tardi?') ?></p>
                        <?php if (!user()->isGuest): ?>
                            <?php
                            echo CHtml::ajaxLink(t('site', 'Aggiungi ai preferiti') .
                                    '<span class="inner">
                                       <span class="icon"></span>
                                    </span>
                                    <span class="counter">' . $favUsers . '</span>', app()->createUrl('productsale/favoriteadd'), array(
                                'type' => 'POST',
                                'dataType' => 'json',
                                'data' => array('id' => $product->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                                'beforeSend' => 'function(){
                                    $("#not_favorite .loader").show();
                                    }',
                                'complete' => 'function(){
                                        $("#not_favorite .loader").hide();
                                    }',
                                'success' => "function(data) {
                                        if(data.success==1) {
                                            $('#favorite-delete .counter').html(data.count);
                                            $('#not_favorite').removeClass('active').addClass('hidden');
                                            $('#is_favorite').removeClass('hidden').addClass('active');
                                        } else {
                                            alert(" . t('site', '"Error! The offer cannot be added as favorite."') . ");
                                        }
                                    }"), array(
                                'id' => 'favorite-add',
                                'class' => 'favorite add',
                                'href' => 'javascript:void(0)'));
                            ?>
                        <?php else: ?>
                            <?php
                            echo CHtml::link(t('site', 'Aggiungi ai preferiti') .
                                    '<span class="inner">
                                       <span class="icon"></span>
                                    </span>
                                    <span class="counter">' . $favUsers . '</span>', app()->createUrl('page/render', array('slug' => 'sign-in')), array(
                                'id' => 'favorite-add',
                                'class' => 'favorite add',
                                'href' => 'javascript:void(0)'
                            ));
                            ?>
                        <?php endif; ?>
                        <div class="loader"></div>
                    </div>

                    <div id="is_favorite" class="row last <?php echo empty($favOffer) ? 'hidden' : 'active'; ?>">
                        <p><?php echo t('site', 'L\'offerta e stata aggiunta ai preferiti') ?></p>
                        <?php
                        echo CHtml::ajaxLink(t('site', 'Cancela dai preferiti') .
                                '<span class="inner">
                                       <span class="icon"></span>
                                    </span>
                                    <span class="counter">' . $favUsers . '</span>', app()->createUrl('productsale/favoritedelete'), array(
                            'type' => 'POST',
                            'dataType' => 'json',
                            'data' => array('id' => $product->id, 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                            'beforeSend' => 'function(){
                                  $("#is_favorite .loader").show();
                                }',
                            'complete' => 'function(){
                                    $("#is_favorite .loader").hide();
                                }',
                            'success' => "function(data) {
                                    if(data.success==1) {
                                         $('#favorite-add .counter').html(data.count);
                                         $('#not_favorite').removeClass('hidden').addClass('active');
                                         $('#is_favorite').removeClass('active').addClass('hidden');
                                    } else {
                                        alert(" . t('site', '"Error while deleting favorite item."') . ");
                                    }
                                }"), array(
                            'id' => 'favorite-delete',
                            'class' => 'favorite delete',
                            'href' => 'javascript:void(0)'));
                        ?> 
                        <div class="loader"></div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="hr2"></div>

            <?php
            $this->widget('zii.widgets.jui.CJuiTabs', array(
                'tabs' => array(
                    t('site', 'Descrizione') => array(
                        'id' => 'tab1',
                        'content' => $this->getController()->renderPartial(
                                'common.blocks.company_store._product_view_description', array('product' => $product), TRUE)
                    ),
//                    t('site', 'Commenti') => array(
//                        'id'=>'tab1',
//                         'content'=>$this->getController()->renderPartial(
//                        'common.blocks.company_store._comments_main',
//                            array('product'=>$product, 'model'=>$model),TRUE)
//                        )
                ),
                'htmlOptions' => array('class' => 'nav-tabs-nd tabs'),
                // additional javascript options for the tabs plugin
                'options' => array(
                    'collapsible' => true,
                ),
            ));
            ?>
        </article>
    </div>
</div>




<?php $this->render('common.blocks.company_store._full_img', array('company' => $company, 'product' => $product)); ?>


<?php
Yii::app()->clientScript->registerScript('ga', '
    
var _gaq = _gaq || [];
_gaq.push(["_setAccount", "UA-46637255-2"]);
_gaq.push(["_setDomainName", "none"]);
_gaq.push(["_setAllowLinker", "true"]);
_gaq.push(["_setCustomVar", 1, "store-id", "'.$company->username.'", 3]);
_gaq.push(["_setCustomVar", 2, "product-id", "'.$product->id.'", 3]);
_gaq.push(["_trackPageview"]);

(function() {
var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
})();
    
', CClientScript::POS_END);
?>

