<?php
$this->pageTitle=t('cms','Manage Menu');
$this->pageHint=t('cms','Here you can manage your menus'); 

$this->breadcrumbs = array(
    t('cms','Manage menu'),
    );
?>

<?php $this->widget('cmswidgets.ModelManageWidget',array('model_name'=>'Menu')); 
?>