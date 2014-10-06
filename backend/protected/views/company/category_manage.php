<?php 
$this->pageTitle=Yii::t('AdminTerm','Manage Companies Categories');
$this->pageHint=Yii::t('AdminTerm','Here you can manage your Categories. <br /> <b>Note: </b>When you delete a Category, all contents which belongs to that Category will be moved to Uncategory'); 
?>
<?php $this->widget('cmswidgets.company.CategoryManageWidget',array()); 
?>