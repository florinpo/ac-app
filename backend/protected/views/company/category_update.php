<?php 
$this->pageTitle=Yii::t('AdminCategory','Update category');
$this->pageHint=Yii::t('AdminCategory','Here you can update information for current category'); 
?>
<?php $this->widget('cmswidgets.company.CategoryUpdateWidget',array()); 
?>