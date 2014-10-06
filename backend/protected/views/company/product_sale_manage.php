<?php 
$this->pageTitle=Yii::t('AdminTerm','Manage Company Products');
$this->pageHint=Yii::t('AdminTerm','Here you can manage products of the current company');
?>
<?php $this->widget('cmswidgets.company.CompanyProductSaleManageWidget',array()); 
?>