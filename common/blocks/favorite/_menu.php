<?php
$this->widget('zii.widgets.CMenu', array(
    'htmlOptions' => array('class' => 'nav clearfix'),
    'id' => 'fav-menu',
    'firstItemCssClass' => 'first',
    'lastItemCssClass' => 'last',
    'items' => array(
        array(
            'label' => t('site', 'Prodotti'),
            'url' => array('page/render', 'slug' => 'preferiti', 'op' => 'prodotti'),
            'active' => !isset($_GET['op']) || $_GET['op'] == 'prodotti' ? true : false
        ),
        array(
            'label' => t('site', 'Negozi'),
            'url' => array('page/render', 'slug' => 'preferiti', 'op' => 'negozi'),
            'active' => isset($_GET['op']) && $_GET['op'] == 'negozi' ? true : false
        )
    ),
));
?>

