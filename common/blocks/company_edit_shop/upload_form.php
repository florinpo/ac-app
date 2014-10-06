
<div class="single-actionbar">
    <!-- The fileinput-button span is used to style the file input field as button -->
    <a href="javascript:void(0);" class="btn-n green-l btn-success fileinput-button">
        <span class="inner">
            <span class="text">
                <i class="icon-upload icon-white"></i>
                <?php echo t('site', 'Carica logo'); ?>
            </span>
            <?php
            if ($this->hasModel()) :
                echo CHtml::activeFileField($this->model, $this->attribute, $htmlOptions) . "\n";
            else :
                echo CHtml::fileField($name, $this->value, $htmlOptions) . "\n";
            endif;
            ?>
        </span>
    </a>
</div>

<!-- The table listing the files available for upload/download -->
<div class="files single"></div>
<!-- The table listing the files available for upload/download -->




