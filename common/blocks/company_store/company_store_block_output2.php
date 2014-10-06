<div class="grid_15 box_shop_content">

    <?php
    $this->widget('zii.widgets.CMenu', array(
        'id' => 'tabnav-shop',
        'items' => $this->menu
    ));
    ?>
    <div class="tabnav-shop-body">
        <section id="about" class="shop-page">
            <h2 class="dash"><?php echo t('site', 'Chi siamo'); ?></h2>
            <div class="description">
                <?php echo nl2br($shop->description); ?>
            </div>
            <div class="info-desc">
                <div class="row clearfix odd">
                    <span class="label"><?php echo t('site', 'Prodotti e servizi:'); ?></span>
                    <span class="data"><?php echo $shop->services; ?></span>
                </div>
                <div class="row clearfix even">
                    <span class="label"><?php echo t('site', 'Aria prodotti e servizi:'); ?></span>
                    <span class="data"><?php echo $shop->serviceLocations; ?></span>
                </div>
                <div class="row clearfix odd">
                    <span class="label"><?php echo t('site', 'Typo impresa:'); ?></span>
                    <span class="data"><?php echo UserCompanyProfile::getStringCtype($company->cprofile->companytype); ?></span>
                </div>
                <div class="row clearfix even">
                    <span class="label"><?php echo t('site', 'Certificati:'); ?></span>
                    <span class="data"><?php echo!empty($shop->certificate) ? $shop->certificate : ''; ?></span>
                </div>
            </div>

            <div id="selected-products" class="nav-tabs-nd">
                <ul class="green-p nav clearfix">
                    <li class="single active arrow"><?php echo CHtml::link(t('site', 'Selected Products'), 'javascript:void(0);', array()) ?></li>
                </ul>
                <div class="nav-body clearfix dashed">
                    <ul class="products_selected_list">
                        <?php foreach ($shop->selectedProducts as $product): ?>
                            <li>
                                <div class="thumbnail"><?php echo $product->selectedImage(180); ?></div>
                                <div class="data">
                                    <?php
                                    echo CHtml::link($product->name, array(
                                        'site/store',
                                        'username' => $company->username,
                                        'page_slug' => 'elenco-vendita',
                                        'prod_id' => $product->id,
                                        'prod_slug' => $product->slug
                                    ));
                                    ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php
            $this->render('common.blocks.company_store._contact', array('company' => $company)); ?>
        </section>
    </div>
</div>

