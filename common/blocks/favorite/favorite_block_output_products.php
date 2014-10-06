<div id="favorite-items" class="nav-tabs-ls">
    <?php $this->render('common.blocks.favorite._menu', array()); ?>
   
    <div class="nav-body results clearfix">
         <?php if ($total > $pageSize): ?>
         <div class="actions-top clearfix">
            <?php
            $this->widget('cms.extensions.customPager.CustomPager', array(
                'currentPage' => $pages->getCurrentPage(),
                'itemCount' => $total,
                'pageSize' => $pageSize,
                'firstPageLabel' => t('site', 'primo'),
                'lastPageLabel' => t('site', 'ultimo'),
                'nextPageLabel' => t('site', 'successivo'),
                'prevPageLabel' => t('site', 'precedente'),
                'htmlOptions' => array('class' => 'custom-pager')
            ));
            ?>
        </div>
        <?php endif; ?>
        
        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider' => $model,
            'itemView' => 'common.blocks.favorite._product_view',
            'template' => '{items}{pager}',
            //'summaryText' => t('site', 'Displaying') . ' {start} - {end} ' . t('site', 'in') . ' {count} ' . t('site', 'results'),
            'summaryText' => t('site', 'Pagina ') . '{page}' . t('site', ' da ') . '{pages}',
            'emptyText' => t('site','Nisun prodotto e stato aggiunto ai favoriti'),
            'pager' => array(
                'cssFile' => '',
                'header' => '',
                'firstPageLabel' => t('site', 'primo'),
                'lastPageLabel' => t('site', 'ultimo'),
                'nextPageLabel' => t('site', 'successivo'),
                'prevPageLabel' => t('site', 'precedente'),
                'maxButtonCount' => 5,
            ),
            'id' => 'items-favorite-products',
            'itemsTagName' => 'ul',
            'itemsCssClass' => 'items-grid-view clearfix',
            'pagerCssClass' => 'pagination pagination-centered nofl',
            'ajaxUpdate' => true,
            'enablePagination' => true,
            'enableSorting' => false,
        ));
        ?>
    </div>
</div>