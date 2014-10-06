<section id="product_results" class="box_round_c results grid_18">
    <?php if ($total > 0): ?>
        <?php
        $catId = numFromString($_GET['subcat']);
        $catname = ProductSaleCategoryList::model()->findByPk($catId)->name;
        ?>
        <h1><?php echo t('site', 'Categoria: ') . '<span>' . $catname . '</span>' . ' - ' . $total . t('site', ' prodotti trovati'); ?></h1>
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
            $this->widget('cms.extensions.customPagers.CustomPagerPNCounter', array(
                //'currentPage' => $pages->getCurrentPage(),
                'itemCount' => $total,
                'pageSize' => $pageSize,
                'firstPageLabel' => t('site', 'primo'),
                'lastPageLabel' => t('site', 'ultimo'),
                'nextPageLabel' => t('site', 'successivo'),
                'prevPageLabel' => t('site', 'precedente'),
                'htmlOptions' => array('class' => 'custom-pager-pn')
            ));
            ?>
        </div>
        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider' => $dataProvider,
            'itemView' => !empty($cList) && $cList == 'grid' ? 'common.blocks.product_listing_category._product_view_grid' : 'common.blocks.product_listing_category._product_view_list',
            'template' => '{items}{pager}',
            //'summaryText' => t('site', 'Displaying') . ' {start} - {end} ' . t('site', 'in') . ' {count} ' . t('site', 'results'),
            'summaryText' => t('site', 'Pagina ') . '{page}' . t('site', ' da ') . '{pages}',
            'pager' => array(
                'cssFile' => '',
                'header' => '',
                'firstPageLabel' => t('site', 'primo'),
                'lastPageLabel' => t('site', 'ultimo'),
                'nextPageLabel' => t('site', 'successivo'),
                'prevPageLabel' => t('site', 'precedente'),
                'maxButtonCount' => 5,
            ),
            'id' => 'items-products',
            'itemsTagName' => 'ul',
            'itemsCssClass' => !empty($cList) && $cList == 'grid' ? 'items-grid-view clearfix' : 'items-list-view clearfix',
            'pagerCssClass' => 'pagination pagination-centered nofl',
            'ajaxUpdate' => false,
            'enablePagination' => true,
            'enableSorting' => false,
        ));
        ?>
    <?php else: ?>
        <h1><?php echo $total . t('site', ' risultati trovati'); ?></h1>
        <div class="empty_results">
            <ul>
                <li><?php echo t('site', 'Va rugam sa verificati cuvantul cautat (minim 2 caractere)'); ?></li>
                <li><?php echo t('site', 'Folositi un cuvant mai general'); ?></li>
                <li><?php echo t('site', 'Va rugam folositi maxim 10 cuvinte la cautare'); ?></li>
            </ul>
        </div>
    <?php endif; ?>
</section>
