<?php
user()->setState('province', 'Torino');

?>
<div class="toogle-panel" id="select-location-panel">
    <a href="#" id="1" class="toggler" title="<?php echo t('site', 'Cambia città') ?>"><span class="view"><?php echo t('site', 'Cambia città') ?></span></a>
   
    <div class="toggle-inner" id="1-info">
        <span class="arrow"></span>
        <?php
        $regions = Region::model()->findAll(array('order' => 'name ASC'));
        $lists = array_chunk($regions, 20);
        foreach ($lists as $k => $list) {
            echo "<ul class='region_list level-1'>";
            foreach ($list as $t => $region) {
                echo "<li>";
                echo CHtml::link($region->name, 'javascript:void(0)', array('class' => 'region'));
                $provinces = Province::model()->findAll(array('condition' => 'regionId=:regionId', 'params' => array(':regionId' => $region->id)));
                if (count($provinces) > 0) {

                    echo "<ul class='provinces level-2'>";
                    foreach ($provinces as $province) {
                        $provinceName = encode($province->name, '-', false);
                        //$url = remove_url_param(array('from'));
                        $url = add_url_param(array('location' => $provinceName));
                        echo '<li>';
                        echo CHtml::link(Chtml::encode($province->name), $url, array('id'=>$province->id));
                        echo '</li>';
                    }

                    echo "</ul>";
                }
                echo "</li>";
            }
            echo "</ul>";
        }
        ?>
    </div>
</div>