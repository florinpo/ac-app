<?php

$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => 'common.blocks.company_store._review',
    'template' => '{items}{pager}',
    //'summaryText' => t('site', 'Pagina ') . '{page}' . t('site', ' da ') . '{pagine}',
    'pager' => array(
        'class' => 'cms.extensions.infiniteScroll.IasPager',
        'rowSelector' => '.row',
        'listViewId' => 'items-shop-reviews',
        'header' => '',
        'loader' => '<img src="' . $layout_asset . '/images/loader.gif"/>',
        'options' => array(
            'history' => false,
            //'triggerPageTreshold' => 2,
            'trigger' => t('site', 'Mostra altro'),
            'onRenderComplete' => 'js:function(items) {
                            $("#items-shop-reviews span[id^=\'rating\'] > input").rating({\'required\':false, \'readOnly\':true});
                         }',
        )
    ),
    'id' => 'items-shop-reviews',
    'itemsTagName' => 'ul',
    'itemsCssClass' => 'reviews-list items clearfix',
    'cssFile' => false,
    'loadingCssClass' => 'list-view-loading-16',
    'ajaxUpdate' => true,
    'enableSorting' => true,
    'afterAjaxUpdate' => 'function(id,data) {
                    $("#items-shop-reviews span[id^=\'rating\'] > input").rating({\'required\':false, \'readOnly\':true});
                }',
));
?>


