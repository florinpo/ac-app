<?php if (isset($menus)): ?>

    <?php
    // we start from level 2 to not make visible the root item
    $level = 2;
    $m = array();
    $menu_name = Menu::model()->findByPk($this->menu_id)->menu_name;
    echo CHtml::openTag('ul', array('id' => encode($menu_name, '-', false), 'class' => encode($menu_name, '-', false)));
    foreach ($menus as $n => $menu) {
        if ($menu['level'] == $level && $n != 0)
            echo CHtml::closeTag('li');
        else if ($menu['level'] > $level)
            echo CHtml::openTag('ul', array('class' => 'level-' . ($menu['level'] - 1)));
        else if ($menu['level'] < $level) {
            echo CHtml::closeTag('li');
            for ($i = $level - $menu['level']; $i; $i--) {
                echo CHtml::closeTag('ul');
                echo CHtml::closeTag('li');
            }
        }
        $slug = isset($_GET['slug']) ? plaintext($_GET['slug']) : '';
        $currentUrl = Yii::app()->baseUrl . '/' . $slug . '/';
        $class = isset($currentUrl) && $currentUrl == $menu['link'] ? 'current' : '';

        // fix for User menu classes icons
        if ($menu_name == 'User menu') {
            if ($n == 0) {
                $class .='login';
            } else if ($n == 1) {
                $class .='register';
            }
        }

        echo CHtml::openTag('li', array('class' => $class));
        echo CHtml::link(CHtml::encode($menu['name']), $menu['link']);
        $level = $menu['level'];
    }
    for ($i = $level - 1; $i; $i--) {
        echo CHtml::closeTag('li');
        echo CHtml::closeTag('ul');
    }
    ?>

    <!-- end navigation bar -->
<?php endif; ?>
