<?php 
$this->pageTitle=Yii::t('cms','Create store');
$this->pageHint=Yii::t('cms','Here you add a new store layout'); 
?>
<?php $this->widget('cmswidgets.store.StoreCreateWidget',array());
?>