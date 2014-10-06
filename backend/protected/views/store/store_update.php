
<?php 
$this->pageTitle=Yii::t('cms','Update current store');
$this->pageHint=Yii::t('cms','Here you can update current store layout'); 
?>
<?php $this->widget('cmswidgets.store.StoreUpdateWidget',array());
?>