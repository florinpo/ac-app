<article id="product-view" class="box_widget item-view grid_18 omega">
     <h1 class="b-dotted"><?php echo $product->name; ?></h1>
     <div id="item-gallery" class="grid_7 omega">
        <div class="img-view">
            <?php
            echo $product->selectedImage(180);
            ?>
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
     <div class="grid_11 alpha">
        <div class="item_details">
            <div class="row">
                <div class="data_label"><?php echo t('site', 'Modelo: '); ?></div>
                <div class="data_name"><?php echo $product->model; ?></div>
            </div>
            <div class="row">
                <div class="data_label"><?php echo t('site', 'Prezzo: '); ?></div>
                <div class="data_name">
                    <?php echo ($product->price > 0) ? $product->price : t('site', 'Non specificato'); ?>
                </div>
            </div>
            <div class="row">
                <div class="data_label"><?php echo t('site', 'Trovato nel: '); ?></div>
                <div class="data_name">
                    <?php
                    $categories = $product->categories;
                    if (count($categories) > 0) {
                        echo "<ul id='product-categories'>";
                        foreach ($categories as $cat) {
                            echo "<li>";
                            echo $this->getCategoryParents($cat->id) . '<br />';
                            echo "</li>";
                        }
                        echo "</ul>";
                    }
                    ?>
                </div>
            </div>
             <div class="row">
                <div class="data_label"><?php echo t('site', 'Attualizato a: '); ?></div>
                <div class="data_name">
                    <?php echo simple_date($product->update_time); ?>
                </div>
            </div>
        </div>
    </div>
      <div id="item-description" class="grid_18 alpha">
        <h2><?php echo t('site', 'Descrizione del prodotto') ?></h2>
        <p> <?php echo nl2br($product->description); ?></p>
    </div>
</article>