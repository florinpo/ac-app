
<div class="fileupload-buttonbar clearfix">

        <!-- The fileinput-button span is used to style the file input field as button -->
        <a href="javascript:void(0);" class="btn-n green-l fileinput-button">
            <span class="inner">
                <span class="text">
                     <i class="icon-upload icon-white"></i>
                    <?php echo t('site', 'Carica logo'); ?>
                </span>
            </span>
            <?php
            if ($this->hasModel()) :
                echo CHtml::activeFileField($this->model, $this->attribute, $htmlOptions) . "\n";
            else :
                echo CHtml::fileField($name, $this->value, $htmlOptions) . "\n";
            endif;
            ?>
        </a>
        <a class="tooltip t-left" title="<?php echo t('site', 'Max 200 kb.');?>"></a>
        
</div>


<!-- The table listing the files available for upload/download -->
<table class="table-upload table-striped">
    <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
</table>

