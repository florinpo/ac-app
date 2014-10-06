<?php
$this->pageTitle=t('cms','Manage current menu items');
$this->pageHint=t('cms','Here you can manage your menu items');
$name = Menu::model()->findByPk((int)($_GET['menu']))->menu_name;
$this->breadcrumbs = array(t('cms','Manage menus')=>array('menu/admin'), $name.' '. t('cms','items'));
?>
<?php 
$this->widget('cmswidgets.page.MenuItemsAdminWidget',array()); 
?>