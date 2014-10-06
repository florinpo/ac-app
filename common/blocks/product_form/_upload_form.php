<div class="fileupload-buttonbar clearfix">

    <a href="javascript:void(0);" class="fileinput-button bLink">
        <span class='icon icon-camera'></span>
        <span><?php echo t('site', 'Add images'); ?></span>

        <?php
        if ($this->hasModel()) :
            $this->formOptions['accept'] = 'image/*';
            echo CHtml::activeFileField($this->model, $this->attribute, $this->formOptions) . "\n";
        else :
            echo CHtml::fileField($name, $this->value, $this->formOptions) . "\n";
        endif;
        ?>
    </a>

</div>

<?php
$product = ProductSale::model()->findByPk($this->parentId);
$class = ($product && $product->imagescount > 0) ? 'clearfix' : 'clearfix hidden';
?>

<div class="f-block <?php echo $class; ?>">

    <!-- The list files available for upload/download -->
    <ul class="files" data-toggle="modal-gallery" data-target="#modal-gallery">
        <?php
        
        if ($product && $product->imagescount > 0):
            foreach ($product->pimages as $k => $img):
                $classSelected = ($product->main_image == $img->id) ? 'selected' : '';
                ?>
                <li id="i-<?php echo $img->id; ?>" class="<?php echo $classSelected; ?>">
                    <a class="delete btn-img t-top" id="deleteimg" href="javascript:void(0);" title="<?php echo t('site', 'Cancel'); ?>"><span class="icon iconl-close"></span></a>
                    <?php if ($product->imagescount > 1): ?>
                        <a class="select btn-img t-bottom" id="mainimg" href="javascript:void(0);" title="<?php echo t('site', 'Imagine principale'); ?>"><span class="icon"></span></a>
                    <?php endif; ?>
                    <div class="f-container">
                        <img src="<?php echo IMAGES_URL . '/img120/' . $img->path; ?>"/>
                    </div>
                </li>
                <?php
            endforeach;
        endif;
        ?>
    </ul>
</div>

