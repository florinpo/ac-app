<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
Yii::app()->clientScript->registerScriptFile($layout_asset . "/js/jquery.infieldlabel.min.js", CClientScript::POS_HEAD);
?>

<?php
Yii::app()->clientScript->registerScript('reviewForm', '
    
    $("input.inField").each(function() {
        var $this = $(this);
        if($this.val() === "") {
            $this.val($this.attr("title"));
        }
        $this.focus(function() {
            if($this.val() === $this.attr("title")) {
                $this.val("");
            }
        });
        $this.blur(function() {
            if($this.val() === "") {
                $this.val($this.attr("title"));
            }
        });
    });
    
', CClientScript::POS_READY);
?>
<?php
$params = $_GET;
//we remove the pagination param
if (isset($params['page'])) {
    unset($params['page']);
}
if (count($params) > 2):
    ?>
    <div class="box_widget grid_6 alpha">
        <div class="header">
            <h3><?php echo t('site', 'Ricerca corrente') ?></h3>
        </div>
        <div class="content">
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
//                if (isset($_GET['price'])) {
//                    $link = Yii::t('site', 'Solo con prezzo');
//                    $url = remove_url_param(array('price'));
//                    echo '<li>';
//                    echo CHtml::link($link, $url);
//                    echo '</li>';
//                }
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
                if (isset($_GET['minprice']) && isset($_GET['maxprice'])) {
                    $link = Yii::t('Global', 'Tra: ') . '&euro; ' . numFromString($_GET['minprice']) . ' - ' . '&euro; ' . numFromString($_GET['maxprice']);
                    $url = remove_url_param(array('minprice', 'maxprice'));
                    echo '<li>';
                    echo CHtml::link($link, $url);
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php
if (!isset($_GET['membership']) || !isset($_GET['price']) ||
        !isset($_GET['province']) || !isset($_GET['cat']) ||
        !isset($_GET['from'])):
    ?>
    <div class="box_widget grid_6 alpha">
        <div class="header">
            <h3><?php echo t('site', 'Filtri di ricerca') ?></h3>
        </div>
        <div class="content">
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

                            <?php if (!isset($_GET['discount'])): ?>
                                <li>
                                    <?php
                                    $url = add_url_param(array('discount' => 'discount'));
                                    echo CHtml::link(t('site', 'Con prezzo promozionale'), $url, array('class' => 'discount'));
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


                <?php
                $counts = array();
                if (isset($_GET['province'])) {
                    $items = array();
                    if (isset($result[4]['matches'])) {
                        foreach ($result[4]['matches'] as $idx => $row) {
                            foreach ($row['attrs']['categoryid'] as $c) {
                                $items[] = $c;
                            }
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

                <?php
                if (!isset($_GET['discount']) && !isset($_GET['minprice']) && !isset($_GET['maxprice'])):
                    ?>

                    <?php
                    $prices = array();

                    if (isset($_GET['province'])) {
                        if (isset($result[4]['matches'])) {
                            foreach ($result[4]['matches'] as $item) {
                                $prices[] = $item['attrs']['price'];
                            }
                        }
                    } else {
                        if (isset($result[3]['matches'])) {
                            foreach ($result[3]['matches'] as $item) {
                                $prices[] = $item['attrs']['price'];
                            }
                        }
                    }
                    if (!empty($prices))
                        $maxPrice = ceil(max($prices));
                    else
                        $maxPrice = '999999';
                    ?>

                    <dt><?php echo t('site', 'Scegli il prezzo:') ?></dt>
                    <dd>
                        <?php echo CHtml::beginForm('', 'get', array('id' => 'priceFilter')); ?>
                        <label><?php echo t('site', 'EUR: ') ?></label>

                        <?php echo CHtml::textField('minprice', '', array('id' => 'min_price', 'size' => 5, 'title' => 0, 'class'=>'inField')); ?>
                        <span>-</span>
                        <?php echo CHtml::textField('maxprice', '', array('id' => 'max_price', 'size' => 5, 'title' => $maxPrice, 'class'=>'inField')); ?>

                        <?php echo CHtml::submitButton(t('site', ''), array('class' => 'btn-i-s', 'name'=>'')); ?>
                        <?php echo CHtml::endForm(); ?>
                    </dd>
                <?php endif; ?>

                <?php
                $counts = array();
                if (isset($_GET['province'])) {
//                    $xitems = array();
//                    $yitems = array();
//                    if (isset($result[1]['matches'])) {
//                        foreach ($result[1]['matches'] as $idx => $row) {
//                            $xitems[] = $row['attrs']['companytype'];
//                        }
//                    }
//
//                    $xitems = array_count_values($xitems);
//
//                    if (isset($result[3]['matches'])) {
//                        foreach ($result[3]['matches'] as $idx => $row) {
//                            $yitems[$row['attrs']['@groupby']] = $row['attrs']['@count'];
//                        }
//                    }
                    //$counts = array_merge_numeric_values($xitems, $yitems);


                    $items = array();
                    if (isset($result[4]['matches'])) {
                        foreach ($result[4]['matches'] as $idx => $row) {
                            $items[] = $row['attrs']['companytype'];
                        }
                    }
                    $counts = array_count_values($items);


                    //print_r(array_merge_numeric_values($xitems, $yitems));
                } else {
                    if (isset($result[2]['matches'])) {
                        foreach ($result[2]['matches'] as $idx => $row) {
                            $counts[$row['attrs']['@groupby']] = $row['attrs']['@count'];
                        }
                    }
                }
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


            </dl>
        </div>
    </div>
<?php endif; ?>