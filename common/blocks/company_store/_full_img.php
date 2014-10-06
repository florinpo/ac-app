<!--- MODAL CONTAINER --->
<div class="modal-container">
    <div id="modal-content">
        <div class="col-l">
            <div class="img_holder">
                <?php
                $selectedImg = $product->selectedImageObj();
                $src = IMAGES_URL . "/img400/" . $selectedImg->path;
                echo Chtml::Image($src, '');
                ?>
            </div>
        </div>
        <div class="col-r">
            <h2><?php echo Chtml::link($product->name, "#", array('onclick' => "$.colorbox.close(); return false;")); ?></h2>
            <span class="provider"><?php echo t('site', 'Prodotto offerto da: ') . "<strong>" . $company->cprofile->companyname . "</strong>"; ?></span>

            <div class="thumbnails_list">
                <?php $images = $product->pimages; ?>
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
                        var list = $('.thumbnails_list ul li a');
                        list.each(function(i) {
                                $(this).removeClass('current');
                        });
                        $(json).each(function(i,val){
                             $('#modal-content .img_holder img').attr('src', '" . IMAGES_URL . "/img400/" . "'+ val.path);
                             $('#modal-content .thumbnails_list li#thumb-'+val.key).find('a').addClass('current');
                             
                        });
                        $('.thumbnails_list ul li:first').addClass('first');
                        
            }"), array(
                            'href' => 'javascript:void(0)',
                            'class' => $class));
                        ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>