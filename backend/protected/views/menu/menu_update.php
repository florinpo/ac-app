<?php 
$this->pageTitle=t('cms','Update Menu');
$this->pageHint=t('cms','Here you can update information for current Menu'); 
$this->breadcrumbs = array(t('cms','Manage menus')=>array('menu/admin'),
    t('cms','Update menu'));
?>
<?php
$this->widget('cmswidgets.page.MenuUpdateWidget',array()); 
?>