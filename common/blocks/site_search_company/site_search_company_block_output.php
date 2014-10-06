<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<section id="shop_results" class="box_round_c results grid_18">
    <?php if (!empty($finalresults)): ?>
        <h1><?php echo '<span>' . ucfirst(encode($_GET['q'], '-', false)) . '</span>' . ' - ' . $resultCount . t('site', ' negozzi trovati'); ?></h1>
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
        <div class="list-view" id="items-shops">
            <ul class="<?php echo!empty($cList) && $cList == 'grid' ? 'shops-grid-view clearfix' : 'shops-list-view clearfix'; ?>">
                <?php foreach ($finalresults as $k => $row): ?>
                    <?php
                    $user_id = $row['attrs']['user_id'];
                    $cuser = $row['attrs']['username'];
                    $cmembership = $row['attrs']['has_membership'];
                    
                    $company = User::model()->findByPk($user_id);
                    $shop = $company->cshop;
                    $cprofile = $company->cprofile;
                  
                    $class = ($k % 2 == 0) ? "first" : "last";
                    $class .= ($cmembership == 1) ? " premium" : "";
                    ?>
                    <li class="clearfix <?php echo $class; ?>">
                        <?php
                        $view = !empty($cList) && $cList == 'grid' ? '_shop_view_grid' : '_shop_view_list';
                        $this->render('common.blocks.site_search_company.' . $view, array(
                            'cuser' => $cuser,
                            'cmembership' => $cmembership,
                            'company' => $company,
                            'cprofile' => $cprofile,
                            'shop' => $shop,
                            'layout_asset' => $layout_asset
                        ));
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="pagination pagination-centered nofl">
                <?php
                $this->widget('CLinkPager', array(
                    'currentPage' => $pages->getCurrentPage(),
                    'header' => '',
                    'firstPageLabel' => t('site', 'primo'),
                    'lastPageLabel' => t('site', 'ultimo'),
                    'nextPageLabel' => t('site', 'successivo'),
                    'prevPageLabel' => t('site', 'precedente'),
                    'maxButtonCount' => 5,
                    'itemCount' => $resultCount,
                    'pageSize' => $this->page_size,
                    'cssFile' => false,
                    'header' => ''
                ));
                ?>
            </div>
        </div>
    <?php else: ?>
        <h1><?php echo '<span>' . $resultCount . '</span>' . t('site', ' risultati trovati'); ?></h1>
        <div class="empty_results">
            <ul>
                <li><?php echo t('site', 'Va rugam sa verificati cuvantul cautat (minim 2 caractere)'); ?></li>
                <li><?php echo t('site', 'Folositi un cuvant mai general'); ?></li>
                <li><?php echo t('site', 'Va rugam folositi maxim 10 cuvinte la cautare'); ?></li>
            </ul>
        </div>
    <?php endif; ?>

</section>