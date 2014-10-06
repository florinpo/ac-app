<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<section id="product_results" class="box_round_c results grid_18">
    <?php if (!empty($finalresults)): ?>
        <h1><?php echo '<span>' . ucfirst(encode($_GET['q'], '-', false)) . '</span>' . ' - ' . $resultCount . t('site', ' offerte trovati'); ?></h1>
        <div class="actions-top">
            <div class="display-type">
                <?php
                $url = Yii::app()->request->url;
                echo CHtml::link(t('site', 'Grid'), $url, array(
                    'submit' => $url,
                    'params' => array('display_type' => 'grid'),
                    'csrf' => true,
                    'class' => !empty($cList) && $cList == 'grid' ? 'btn-grid active' : 'btn-grid')
                )
                ?>

                <?php
                $url = Yii::app()->request->url;
                echo CHtml::link(t('site', 'List'), $url, array(
                    'submit' => $url,
                    'params' => array('display_type' => 'list'),
                    'csrf' => true,
                    'class' => empty($cList) || $cList == 'list' ? 'btn-list active' : 'btn-list')
                )
                ?>

            </div>

            <?php
            $this->widget('cms.extensions.customPagerPN.CustomPagerPN', array(
                'currentPage' => $pages->getCurrentPage(),
                'itemCount' => $resultCount,
                'pageSize' => $this->page_size,
                'firstPageLabel' => t('site', 'primo'),
                'lastPageLabel' => t('site', 'ultimo'),
                'nextPageLabel' => t('site', 'successivo'),
                'prevPageLabel' => t('site', 'precedente'),
                'htmlOptions' => array('class' => 'custom-pager-pn')
            ));
            ?>
        </div>
        <div class="list-view" id="items-products">
            <ul class="<?php echo!empty($cList) && $cList == 'grid' ? 'items-grid-view clearfix' : 'items-list-view clearfix'; ?>">
                <?php //print_r($finalresults); ?>
                <?php foreach ($finalresults as $k => $row): ?>
                    <?php
                    $id = $row['id'];
                    $product = ProductSale::model()->findByPk($id);
                    $companyId = $row['attrs']['companyid'];
                    $cmembership = $row['attrs']['has_membership'];

                    $company = User::model()->findByPk($companyId);
                    $cprofile = $company->cprofile;

                    $class = ($k % 2 == 0) ? "first" : "last";
                    $class .= ($cmembership == 1) ? " premium" : "";
                    ?>
                    <li class="clearfix <?php echo $class; ?>">
                        <?php
                        $view = !empty($cList) && $cList == 'grid' ? '_product_view_grid' : '_product_view_list';
                        $this->render('common.blocks.site_search_product_sale.' . $view, array(
                              'product' => $product,
                              'company' => $company,
                              'cprofile' => $cprofile,
                              'cmembership' => $cmembership,
                              'layout_asset' => $layout_asset
                        ));
                        ?>
                    </li>

                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>




