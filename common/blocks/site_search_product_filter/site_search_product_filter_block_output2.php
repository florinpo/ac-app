<?php
$params = $_GET;
//we remove the pagination param
if (isset($params['page'])) {
    unset($params['page']);
}
if (count($params) > 2):
    ?>
    <div class="box_widget grid_6 alpha">
        <div class="widget_header_l">
            <h3><?php echo t('site', 'Ricerca corrente') ?></h3>
        </div>
        <ul class="active_filters">
            <?php
            if (isset($_GET['q'])) {
                $link = str_replace('-', ' ', ucfirst($_GET['q']));
                echo '<li>';
                echo CHtml::link($link, "javascript:void(0)");
                echo '</li>';
            }
            if (isset($_GET['membership'])) {
                $link = Yii::t('site', 'Solo membri premium');
                $url = remove_url_param(array('membership'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            if (isset($_GET['price'])) {
                $link = Yii::t('site', 'Solo con prezzo');
                $url = remove_url_param(array('price'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            if (isset($_GET['discount'])) {
                $link = Yii::t('site', 'Con prezzo promozionale');
                $url = remove_url_param(array('discount'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            if (isset($_GET['cat'])) {
                $link = Yii::t('site', 'Categoria: ') . ProductSaleCategoryList::model()->findByPk(numFromString($_GET['cat']))->name;
                $url = remove_url_param(array('cat'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            if (isset($_GET['from'])) {
                $link = Yii::t('site', 'Azienda: ') . UserCompanyProfile::getStringCtype(numFromString($_GET['from']));
                $url = remove_url_param(array('from'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            if (isset($_GET['province'])) {
                $link = Yii::t('site', 'Provincia: ') . Province::model()->findByPk(numFromString($_GET['province']))->name;
                echo '<li>';
                $url = remove_url_param(array('province'));
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            if (isset($_GET['maxprice']) && empty($_GET['minprice'])) {
                $link = Yii::t('Global', 'Sotto i: ') . numFromString($_GET['maxprice']) . ' &euro;';
                $url = remove_url_param(array('maxprice'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }

            if (isset($_GET['minprice']) && isset($_GET['maxprice'])) {
                $link = Yii::t('Global', 'Tra: ') . numFromString($_GET['minprice']) . ' &euro;' . ' - ' . numFromString($_GET['maxprice']) . ' &euro;';
                $url = remove_url_param(array('minprice', 'maxprice'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }

            if (isset($_GET['minprice']) && empty($_GET['maxprice'])) {
                $link = Yii::t('Global', 'Piu di: ') . numFromString($_GET['minprice']) . ' &euro;';
                $url = remove_url_param(array('minprice'));
                echo '<li>';
                echo CHtml::link($link, $url);
                echo '</li>';
            }
            ?>
        </ul>
    </div>
<?php endif; ?>

<?php
if (!isset($_GET['membership']) || !isset($_GET['price']) ||
        !isset($_GET['province']) || !isset($_GET['cat']) ||
        !isset($_GET['from'])):
    ?>
    <div class="box_widget grid_6 alpha">
        <div class="widget_header">
            <h3><?php echo t('site', 'Filtri di ricerca') ?></h3>
        </div>
        <dl class="filters-cat">
            <?php if (!isset($_GET['membership']) || !isset($_GET['price'])): ?>
                <dt><?php echo t('site', 'Opzioni:') ?></dt>
                <dd>
                    <ul class="options-primary">
                        <?php if (!isset($_GET['membership'])): ?>
                            <li>
                                <?php
                                $url = add_url_param(array('membership' => 'premium'));
                                echo CHtml::link(t('site', 'Membri premium'), $url, array('class' => 'premium'));
                                ?>
                            </li>
                        <?php endif; ?>
                        <?php if (!isset($_GET['price']) && !isset($_GET['discount'])): ?>
                            <li>
                                <?php
                                $url = add_url_param(array('price' => 'price'));
                                echo CHtml::link(t('site', 'Con prezzo'), $url, array('class' => 'price'));
                                ?>
                            </li>
                        <?php endif; ?>
                        <?php if (!isset($_GET['discount'])): ?>
                            <li>
                                <?php
                                $url = add_url_param(array('discount' => 'discount'));
                                echo CHtml::link(t('site', 'Sconti'), $url, array('class' => 'discount'));
                                ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </dd>
            <?php endif; ?>
            <?php if (!isset($_GET['province'])): ?>
                <dt><?php echo t('site', 'Citta:') ?></dt>
                <dd class="province-d">
                    <div id="fop-1" class="toogle-panel" style="z-index: 1">
                        <a href="#" id="1" class="toggler" title="<?php echo t('site', 'Cambia città') ?>"><span class="view"><?php echo t('site', 'Cambia città') ?></span></a>
                        <div class="toggle-inner" id="1-info">
                            <?php
                            $regions = Region::model()->findAll(array('order' => 'name ASC'));
                            $lists = array_chunk($regions, 5);
                            foreach ($lists as $k => $list) {
                                echo "<ul class='region_list'>";
                                foreach ($list as $t => $region) {
                                    echo "<li>";
                                    echo Chtml::link($region->name, 'javascript:void(0)', array('class' => 'region'));
                                    $provinces = Province::model()->findAll(array('condition' => 'regionId=:regionId', 'params' => array(':regionId' => $region->id)));
                                    if (count($provinces) > 0) {

                                        echo "<ul class='provinces'>";
                                        foreach ($provinces as $province) {
                                            $provinceName = encode($province->name, '-', false);
                                            //$url = remove_url_param(array('from'));
                                            $url = add_url_param(array('province' => $provinceName . '-' . $province->id));
                                            echo '<li>';
                                            echo CHtml::link(Chtml::encode($province->name), $url);
                                            echo '</li>';
                                        }

                                        echo "</ul>";
                                    }
                                    echo "</li>";
                                }
                                echo "</ul>";
                            }
                            ?>
                            <a id="cl-1" class="close" href="javascript:void(0)">Chiudi</a>
                        </div>
                    </div>
                </dd>
            <?php endif; ?>

            <?php if (!empty($result[1]) && !empty($result[1]['matches'])): ?>
                <?php
                $counts = array();
                if (isset($_GET['province'])) {
                    $items = array();
                    if (isset($result[1]['matches'])) {
                        foreach ($result[1]['matches'] as $idx => $row) {
                            foreach ($row['attrs']['categoryid'] as $c) {
                                $items[] = $c;
                            }
                        }
                    }

                    if (isset($result[2]['matches'])) {
                        foreach ($result[2]['matches'] as $idx => $row) {
                            $items[] = $row['attrs']['@groupby'];
                        }
                    }

                    $counts = array_count_values($items);
                } else {
                    if (isset($result[1]['matches'])) {
                        foreach ($result[1]['matches'] as $idx => $row) {
                            $counts[$row['attrs']['@groupby']] = $row['attrs']['@count'];
                        }
                    }
                }
                ?>
                <?php if (!isset($_GET['cat'])): ?>
                    <dt><?php echo t('site', 'Categorie') ?></dt>
                    <dd>
                        <?php
                        echo '<ul>';
                        foreach ($counts as $k => $count) {
                            $name = ProductSaleCategoryList::model()->findByPk($k)->name . ' (' . $count . ')';
                            $slug = ProductSaleCategoryList::model()->findByPk($k)->slug;
                            $url = add_url_param(array('cat' => $slug . '-cat-' . $k));
                            echo '<li>';
                            echo CHtml::link($name, $url);
                            echo '</li>';
                        }
                        echo '</ul>';
                        ?>
                    </dd>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (isset($_GET['price']) || isset($_GET['discount']) && !isset($_GET['minprice']) && !isset($_GET['maxprice'])): ?>
                <dt><?php echo t('site', 'Scegli il prezzo:') ?></dt>
                <dd>
                    <?php echo CHtml::beginForm('', 'post', array('id' => 'priceFilter')); ?>
                <label><?php echo t('site', 'Euro: ') ?></label>
                <?php echo CHtml::textField('minp', '', array('id' => 'min_price', 'size' => 5)); ?>
                <span>-</span>
                <?php echo CHtml::textField('maxp', '', array('id' => 'max_price', 'size' => 5)); ?>
                <?php echo CHtml::submitButton(t('site', 'Invia'), array()); ?>
                <?php echo CHtml::endForm(); ?>
                </dd>
            <?php endif; ?>
            <?php if (!empty($result[2]) && !empty($result[2]['matches'])): ?>
                <?php
                $counts = array();
                if (isset($_GET['province'])) {
                    $items = array();
                    if (isset($result[1]['matches'])) {
                        foreach ($result[1]['matches'] as $idx => $row) {
                            foreach ($row['attrs']['categoryid'] as $c) {
                                $items[] = $c;
                            }
                        }
                    }

                    if (isset($result[3]['matches'])) {
                        foreach ($result[3]['matches'] as $idx => $row) {
                            $items[] = $row['attrs']['@groupby'];
                        }
                    }

                    $counts = array_count_values($items);
                } else {
                    if (isset($result[2]['matches'])) {
                        foreach ($result[2]['matches'] as $idx => $row) {
                            $counts[$row['attrs']['@groupby']] = $row['attrs']['@count'];
                        }
                    }
                }
                //print_r($counts);
                ?>
                <?php if (!isset($_GET['from'])): ?>
                    <dt><?php echo t('site', 'Tipo azienda') ?></dt>
                    <dd>
                        <?php
                        echo '<ul>';
                        foreach ($counts as $k => $count) {
                            $name = UserCompanyProfile::getStringCtype($k) . ' (' . $count . ')';
                            $slug = strtolower(UserCompanyProfile::getStringCtype($k));
                            $url = add_url_param(array('from' => $slug . '-dom-' . $k));
                            echo '<li>';
                            echo CHtml::link($name, $url);
                            echo '</li>';
                        }
                        echo '</ul>';
                        ?>
                    </dd>
                <?php endif; ?>
            <?php endif; ?>    
        </dl>
    </div>
<?php endif; ?>