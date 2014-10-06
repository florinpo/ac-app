
<div class="box_widget bwDefault">
    <div class="header bwHeadLight">
        <span class="hicon"><input type="checkbox" id="Id_all" name="Id_all" value="1" class="select-on-check-all"></span>
        <?php echo CHtml::link("<span class='icon icon-plot'></span><span>" . t('site', 'Plot selected') . "</span>", 'javascript:void(0)', array('class' => 'buttonS bDefault disabled btn btn-grid floatL', 'id' => 'plotselected')); ?>
        <div class="date-info floatL">
            <?php echo date('j M Y', time() - 6 * 24 * 3600) . ' - ' . date('j M Y', time());?>
        </div>
        <?php
        if ($total > 0) {
            $this->widget('cms.extensions.customPagers.CustomPagerPNCounter', array(
                'id' => 'statistics-grid-pagination',
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
        <div class="gr-filter-form grid_9 floatL">
            <?php echo CHtml::beginForm(array('page/render', 'slug' => plaintext($_GET['slug'])), 'get', array('id' => 'statistics-filter-form', 'class'=>'filter-grid-form')); ?>
           
                <div class="labelIn filter">
                    <label for="keyword"><?php echo t('site', 'type to filter...'); ?></label>
                    <?php
                    echo CHtml::textField('keyword', '', array('class' => 'keyword', 'autocomplete' => 'off'));
                    ?>
                    <?php echo CHtml::SubmitButton(t('site', 'Search'), array('id' => 'filter-submit', 'class'=>'btn-filter')); ?>
                </div>
            <?php echo CHtml::endForm(); ?>
        </div>
    </div>
    <?php
    $loader = "<div class='loading-wrapper'></div><div class='loader-indicator loader-label-30'><span class='loader-txt'>" . t('site', 'Caricamento') . "<span></div>";
    $pre_html = '<table class="product-items">
                <thead>
                     <th class="checkbox_column">
                       <span class="icon icond-arrows-table"></span>
                    </th>
                    <th class="grid_left">' . t('site', 'Offerta') . '</th>
                    <th>' . t('site', 'Visits') . '</th>
                    <th>' . t('site', 'Sales') . '</th>
                </thead>
                <tbody>';
    $post_html = '</tbody></table>';

    $this->widget('cms.extensions.customListsView.PlainCListView', array(
        'id' => 'statistics-grid',
        'htmlOptions' => array('class' => 'grid-view grDefault clearfix'),
        'dataProvider' => $dataProvider,
        'itemView' => 'common.blocks.statistics._item',
        'cssFile' => false,
        'preItemsTag' => $pre_html,
        'postItemsTag' => $post_html,
        'template' => "{items}{summary}{pager}",
        'summaryText' => t('cms', 'Displaying') . ' {start} - {end} ' . t('cms', 'in') . ' {count} ' . t('cms', 'results'),
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
        'beforeAjaxUpdate' => 'function(){
                    $(".loading-wrapper").show();
                    $(".loader-indicator").show();
                }',
        'afterAjaxUpdate' => 'function(){
                   $.statistics.updateStatistics();
                 }',
        'enablePagination' => true,
    ));
    ?>
</div>






