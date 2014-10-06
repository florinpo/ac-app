<?php
$params = $_GET;
//we remove the pagination param
if (isset($params['page'])) {
    unset($params['page']);
}
if (count($params) > 3):
    ?>
    <div class="box_widget grid_6 alpha">
        <div class="header">
            <h3><?php echo t('site', 'Ricerca corrente') ?></h3>
        </div>
        <div class="content">
            <ul class="active_filters">
                <?php
                if (isset($_GET['subcat'])) {
                    $link = ProductSaleCategoryList::model()->findByPk(numFromString($_GET['subcat']))->name;
                    echo '<li>';
                    echo CHtml::link($link, array('page/render',
                        'slug' => plaintext($_GET['slug']),
                        'cat' => plaintext($_GET['cat']),
                        'subcat' => plaintext($_GET['subcat'])
                    ));
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
                    $link = Yii::t('site', 'Con prezzo');
                    $url = remove_url_param(array('discount'));
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
                ?>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
<?php endif; ?>

<?php if (!isset($_GET['province']) || !isset($_GET['from']) || !isset($_GET['membership'])): ?>
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
                            <?php if (!isset($_GET['price'])): ?>
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
                        <div id="fop-1" class="toogle-panel">
                            <a href="#" id="1" class="toggler" title="<?php echo t('site', 'Cambia città') ?>"><span class="view"><?php echo t('site', 'Cambia città') ?></span></a>
                            <div class="toggle-inner" id="1-info">
                                <?php
                                $regions = Region::model()->findAll(array('order' => 'name ASC'));
                                $lists = array_chunk($regions, 5);
                                foreach ($lists as $k => $list) {
                                    echo "<ul class='region_list level-1'>";
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

                                <a id="cl-1" class="close" href="javascript:void(0)"></a>
                            </div>
                        </div>
                    </dd>
                <?php endif; ?>
                <?php if (!isset($_GET['from'])): ?>
                    <dt><?php echo t('site', 'Tipo azienda') ?></dt>
                    <dd class="typo-d">
                        <div id="fop-2" class="toogle-panel">
                            <a href="#" id="2" class="toggler" title="<?php echo t('site', 'Typo azienda') ?>"><span class="view"><?php echo t('site', 'Typo azienda') ?></span></a>
                            <div class="toggle-inner" id="2-info">
                                <?php
                                $types = array(
                                    '1' => t('cms', 'Manufacturer'),
                                    '2' => t('cms', 'Distributor'),
                                    '3' => t('cms', 'Wholesaler'),
                                    '4' => t('cms', 'Retailer'),
                                    '5' => t('cms', 'Service provider'),
                                    '6' => t('cms', 'Intermediate'),
                                    '7' => t('cms', 'Importer')
                                );
                                echo "<ul class='dom_list'>";
                                foreach ($types as $k => $type) {
                                    $typeName = encode($type, '-', false);
                                    $url = add_url_param(array('from' => $typeName . '-dom-' . $k));
                                    echo "<li>";
                                    echo CHtml::link(Chtml::encode($type), $url);
                                    echo "<li>";
                                }
                                echo "</ul>";
                                ?>
                                <a id="cl-2" class="close" href="javascript:void(0)"></a>
                            </div>
                        </div>
                    </dd>
                <?php endif; ?>

            </dl>
            <div class="clear"></div>
        </div>
    </div>
<?php endif; ?>

