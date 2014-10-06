<?php 
$this->pageTitle=Yii::t('AdminCategory','Add new category');
$this->pageHint=Yii::t('AdminCategory','Here you can add new category for companies'); 
?>
<?php 

$this->widget('cmswidgets.product_sale.CategoryCreateWidget',array()); 
?>