<?php
$this->widget('cms.extensions.tooltipster.tooltipster', array(
    'identifier' => '.t-middle',
    'options' => array(
        'trigger' => 'hover',
        'speed' => '150'
        ))
);
?>
<section id="products-manage">
    <div class="grid_19 omega">
        <?php $this->render('cmswidgets.views.notification_frontend'); ?>
    </div>
    <div class="box_round_c grid_19 omega">
        <h1><?php echo t('site', 'Manage products'); ?></h1>

        <p class="hint"><span class="icon icon-info-sign"></span><?php echo t('site', 'On the main page of your store you can add  most popular 4 offers. All you have to do is make the offers visible in main page.') ?></p>

        <div class="box_widget bwDefault">
            <div class="header bwHeadLight">
                <span class="hicon"><input type="checkbox" id="Id_all" name="Id_all" value="1" class="select-on-check-all"></span>
                <?php echo CHtml::link("<span class='icon icon-remove'></span><span>" . t('site', 'Delete selected') . "</span>", 'javascript:void(0)', array('class' => 'buttonS bDefault disabled btn btn-grid', 'id' => 'deleteselected')); ?>
                <?php
                if($total>0){
                $this->widget('cms.extensions.customPagers.CustomPagerPNCounter', array(
                    'id' => 'product-grid-pagination',
                    'itemCount' => $total,
                    'pageSize' => $pageSize,
                    'nextPageLabel' => "<span class='icon icon-arrow-right-narrow'></span>",
                    'prevPageLabel' => "<span class='icon icon-arrow-left-narrow'></span>",
                    'htmlOptions' => array('class' => 'pagination-pn pnDefault pnS floatR')
                ));
                }
                ?>
                <div class="clear"></div>
            </div>
            <div class="grbar clearfix">
                <div class="gr-search-form floatL">
                    <?php echo CHtml::beginForm(array('page/render', 'slug' => plaintext($_GET['slug'])), 'get', array('id' => 'quick-search')); ?>
                    <div class="row">
                        <label><?php echo t('site', 'Search:'); ?></label>
                        <div class="labelIn">
                            <label for="q"><?php echo t('site', 'type to filter...'); ?></label>
                            <?php
                            echo CHtml::activeTextField($search, 'keyword', array('class' => 'keyword', 'name' => 'q', 'autocomplete' => 'off'));
                            ?>
                        </div>

                    </div>
                    <?php echo CHtml::endForm(); ?>
                </div>
                <div class="gr-filters floatR">
                    <div class="btn-group floatR noBtn"> <!-- start menu group -->
                        <?php echo CHtml::link("<span class='icon icon-reorder'></span>" . t('site', 'Vizualiza') . '<span class="caret"></span>', 'javascript:void(0)', array('class' => 'trigger bLink', 'data-toggle' => 'dropdown')); ?>

                        <div class="menu-group dropdown-menu">
                            <div class="menu-title"><?php echo t('site', 'Ordinato per:'); ?></div>
                            <ul class="first">
                                <li> <?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'last updated'), 'javascript:void(0)', array('id' => 'update-desc', 'class' => 'filter-link bLink date checked')); ?> </li>
                                <li> <?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'the newest'), 'javascript:void(0)', array('id' => 'create-asc', 'class' => 'filter-link bLink date')); ?> </li>
                                <li> <?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'the oldest'), 'javascript:void(0)', array('id' => 'create-desc', 'class' => 'filter-link bLink date')); ?> </li>
                            </ul>
                            <div class="menu-title"><?php echo t('site', 'Filtri:') ?></div>
                            <ul>
                                <li> <?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'tutti'), 'javascript:void(0)', array('id' => 'type-all', 'class' => 'filter-link bLink type checked')); ?> </li>
                                <li> <?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'active'), 'javascript:void(0)', array('id' => 'type-active', 'class' => 'filter-link bLink type')); ?> </li>
                                <li><?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'pending'), 'javascript:void(0)', array('id' => 'type-pending', 'class' => 'filter-link bLink type')); ?> </li>
                                <li><?php echo CHtml::link("<span class='icon icon-ok'></span>" . t('site', 'request editing'), 'javascript:void(0)', array('id' => 'type-reqedit', 'class' => 'filter-link bLink type')); ?> </li>
                            </ul>
                        </div>
                    </div> <!-- end menu group -->
                </div>
            </div>
            <?php
            $visible = !isset($_GET['status']) || $_GET['status'] == 'active' ? true : false;

            $loader = "<div class='loading-wrapper'></div><div class='loader-indicator loader-label-30'><span class='loader-txt'>" . t('site', 'Caricamento') . "<span></div>";

            $pre_html = '<table class="product-items">
                <thead>
                  <tr>
                    <th class="checkbox_column">
                       <span class="icon icond-arrows-table"></span>
                    </th>
                    <th class="grid_left">' . t('site', 'Offerta') . '</th>
                    <th>' . t('site', 'Status') . '</th>
                        </tr>
                </thead>
                <tbody>';
            $post_html = '</tbody></table>';

            $this->widget('cms.extensions.customListsView.PlainCListView', array(
                'id' => 'product-grid',
                'htmlOptions' => array('class' => 'grid-view grDefault clearfix'),
                'dataProvider' => $dataProvider,
                //'itemView' => 'common.blocks.messages._item_'.$folder,
                'itemView' => 'common.blocks.product_manage._item',
                'cssFile' => false,
                'preItemsTag' => $pre_html,
                'postItemsTag' => $post_html,
                'template' => $loader . "{items}{summary}{pager}",
                'summaryText' => t('cms', 'Displaying') . ' {start} - {end} ' . t('cms', 'in') . ' {count} ' . t('cms', 'results'),
                'emptyText' => t('cms', 'There is no data for this view.'),
                'pager' => array(
                    'cssFile' => '',
                    'header' => '',
                    'firstPageLabel' => t('site', 'primo'),
                    'lastPageLabel' => t('site', 'ultimo'),
                    'nextPageLabel' => t('site', 'successivo'),
                    'prevPageLabel' => t('site', 'precedente'),
                    'maxButtonCount' => 5,
                ),
                'pagerCssClass' => 'pagination-grid floatR',
                'itemsTagName' => 'table',
                'loadingCssClass' => '',
                'ajaxUpdate' => true,
                'enablePagination' => true,
                'beforeAjaxUpdate' => 'function(){
                    $(".loading-wrapper").show();
                    $(".loader-indicator").show();
                }',
                'afterAjaxUpdate' => 'function(){
                   $.productsManage.updateProducts();
                   url = $.fn.yiiListView.getUrl("product-grid");
                   $.productsManage.updatePagination(url);
                 }'
            ));
            ?>
        </div>
    </div>
</section>


