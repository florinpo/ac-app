<?php
$this->pageTitle=t('cms','Update menu item');
$this->pageHint=t('cms','Here you update current menu item');
$name = Menu::model()->findByPk((int)($_GET['menu']))->menu_name;
$this->breadcrumbs = array(
    $name.' '. t('cms','items') => array('menuitems/admin', 'menu'=>$_GET['menu']),
    t('cms','Update menu item'),
    );
?>

<?php $this->widget('cmswidgets.page.MenuItemsUpdateWidget',array()); 
?>