
<?php
$this->pageTitle=t('cms','Add new menu');
$this->pageHint=t('cms','Here you can add new menu for Site'); 

$this->breadcrumbs = array(t('cms','Manage menus')=>array('menu/admin'),
    t('cms','Add new menu'));
?>

<?php $this->widget('cmswidgets.page.MenuCreateWidget',array()); 
?>