
<?php
$this->pageTitle=t('cms','Add new item');
$this->pageHint=t('cms','Here you can add new item to the current menu');
$name = Menu::model()->findByPk((int)($_GET['menu']))->menu_name;
$this->breadcrumbs = array(
    $name.' '. t('cms','items') => array('menuitems/admin', 'menu'=>$_GET['menu']),
    t('cms','Add new item'),
    );
?>


<?php 

$this->widget('cmswidgets.page.MenuItemsCreateWidget',array()); 
?>