
<?php
$form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => false,
        ));
?>

<div class="row">
<?php
echo CHtml::radioButtonList('layout', $cprofile->storeId, CompanyStore::imageList(CHtml::listData($layouts,'id','image')), array('template'=>'<div class="rb" style="width:110px; display:block; float:left">{input}<br /> {label}</div>'));
?>
    </div>

<div class="row buttons">
    <?php echo CHtml::submitButton(t('site','Save'), array('class' => 'bebutton')); ?>
</div>

<?php $this->endWidget(); ?>