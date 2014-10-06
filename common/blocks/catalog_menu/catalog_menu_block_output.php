
<!-- begin catalog-menu -->
<div class="mega-menu-wrapper grid_5">
    <div class="drop-down-trigger">
         <?php echo CHtml::link(t('site', 'Catalogo prodotti'), array('page/render', 'slug' => 'prodotti')); ?>
    </div>
    <div class="mega-menu drop-down-pannel">
        <?php
//        $menus = ProductSaleCategoryList::model()->findAll(array('condition' => 'level=1'));
//        echo CHtml::openTag('ul', array('id' => 'mega-m', 'class' => 'menu'));
//        foreach ($menus as $menu) {
//            $children = $menu->descendants()->findAll(array('condition' => 'level=3'));
//            echo CHtml::openTag('li', array());
//            echo CHtml::link(CHtml::encode($menu->name),array('page/render', 'slug'=>'prodotti', 'cat'=>$menu->slug."-".$menu->id));
//            
//            if (count($children) > 0) {
//                echo CHtml::openTag('ul', array('class' => 'level-' . $menu->level - 1, 'style' => 'margin-left:10px;'));
//                foreach ($children as $child) {
//                    echo CHtml::openTag('li', array());
//                    echo $child->name;
//                    echo CHtml::closeTag('li');
//                }
//                echo CHtml::closeTag('ul');
//            }
//
//            echo CHtml::closeTag('li');
//        }
//        echo CHtml::closeTag('ul');
        ?>
        <ul id="mega-m" class="menu">
            <li>
                <?php echo CHtml::link(t('site', 'Top categorii'), array('page/render', 'slug' => 'prodotti')); ?>
                <ul class="level-2 head-dropdown">
                    <?php
                    $menus = ProductSaleCategoryList::model()->findAll(array('condition' => 'level=3', 'limit' => '30'));

                    $lists = array_chunk($menus, 10);
                    foreach ($lists as $k => $list) {
                        echo "<li>";
                            echo "<ul>";
                            foreach ($list as $t => $menu) {
                                echo "<li>";
                                echo Chtml::link($menu->name, 'javascript:void(0)', array());
                                echo "</li>";
                            }
                            echo "</ul>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </li>
            <li>
                <?php echo CHtml::link(t('site', 'Fashion & Beauty'), array('page/render', 'slug' => 'prodotti')); ?>
                <ul class="level-2 head-dropdown">
                    <?php
                    $menus = ProductSaleCategoryList::model()->findAll(array('condition' => 'level=3', 'limit' => '30'));

                    $lists = array_chunk($menus, 10);
                    foreach ($lists as $k => $list) {
                        echo "<li>";
                            echo "<ul>";
                            foreach ($list as $t => $menu) {
                                echo "<li>";
                                echo Chtml::link($menu->name, 'javascript:void(0)', array());
                                echo "</li>";
                            }
                            echo "</ul>";
                        echo "</li>";
                    }
                    ?>
            </li>
            
        </ul>
    </div>
</div>
<!-- end catalog-menu -->