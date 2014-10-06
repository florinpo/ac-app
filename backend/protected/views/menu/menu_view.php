<?php 
$this->pageTitle=t('cms','Menu details');
$this->breadcrumbs = array(t('cms','Manage menus')=>array('menu/admin'),
    t('cms','View menu'));
$this->widget('cmswidgets.ModelViewWidget',array('model_name'=>'Menu')); 
?>