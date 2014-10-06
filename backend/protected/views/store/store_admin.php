<?php 
$this->pageTitle=Yii::t('cms','Manage store');
$this->pageHint=Yii::t('cms','Here you can manage your store gallery'); 
?>

<?php $this->widget('cmswidgets.store.StoreManageWidget',array());
?>