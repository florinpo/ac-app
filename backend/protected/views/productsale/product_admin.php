<?php 
$this->pageTitle=Yii::t('AdminProduct','Manage Company Products');
$this->pageHint=Yii::t('AdminProduct','Here you can manage products of the current company');
?>
<?php $this->widget('cmswidgets.product_sale.ProductSaleManageWidget',array()); 
?>