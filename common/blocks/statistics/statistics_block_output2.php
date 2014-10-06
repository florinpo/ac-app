
<?php
$date_from_min = '29-10-2013'; // suppose this will be the date since the store is available
$date_from_max = date('d-m-Y', time() - 7 * 24 * 3600); // today - 7 days

$date_to_min = date('d-m-Y', strtotime($date_from_max) + 7 * 24 * 3600); // date_from_max + 7 days
$date_to_max = date('d-m-Y', time());
?>
<section id="statistics">
    <div class="box_round_c grid_19">

        <h1><?php echo t('site', 'Messagi'); ?></h1>

        <div id="c-overview" class="chart-overview" style="margin-left:50px;margin-top:20px;width:400px;height:90px;">

        </div>

        <div class="date-form">

            <div class="grid_6">
                <label><?php echo t('site', 'Start date'); ?></label>
                <?php
                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_from',
                    'language' => 'it',
                    // additional javascript options for the date picker plugin
                    'options' => array(
                        'showAnim' => 'fade',
                        'dateFormat' => 'dd-mm-yy',
                        'minDate' => $date_from_min, // minimum date
                        'maxDate' => $date_from_max, // maximum date
                        'onSelect' => 'js: function(date_from) {
            
                            var date_to, parsedDate, date_to_min;
                            
                            if($("#date_to").val().length == 0) {
                                $("#date_to").val($.statistics.getEndDate());
                            } 
                            date_to = $("#date_to").val(); 
                            
                            var data = {
                                "date-from": date_from,
                                "date-to": date_to,
                                "YII_CSRF_TOKEN": $.statistics.csrf
                            };
                            
                            $("#date_to").datepicker( "option", "minDate", $.statistics.getEndDateMin(date_from));
                            $.statistics.ajaxTotalVisits(date_from, date_to);
                }',
                    )
                ));
                ?>
            </div>
            <div class="grid_6">
                <label><?php echo t('site', 'End date'); ?></label>
                <?php
                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_to',
                    'language' => 'it',
                    // additional javascript options for the date picker plugin
                    'options' => array(
                        'showAnim' => 'fade',
                        'dateFormat' => 'dd-mm-yy',
                        'minDate' => $date_to_min, // minimum date
                        'maxDate' => $date_to_max, // maximum date
                        'onSelect' => 'js: function(date_to) {
                            var date_from;
                            if($("#date_from").val().length == 0) {
                                $("#date_from").val($.statistics.getStartDate());
                            }
                            date_from = $("#date_from").val();
                            $.statistics.ajaxTotalVisits(date_from, date_to);
    
                    }',
                    )
                ));
                ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="chart-wrapper clearfix">
            <div class="loading-wrapper"></div>
            <div class="loader-indicator loader-label-30">
                <span class="loader-txt">
                    <?php echo t('site', 'Caricamento'); ?>
                </span>
            </div>
            <div id="c-placeholder" class="chart chart-placeholder"></div>
        </div>
        
      
        
        
        
        
        <div class="box_widget bwDefault">
            <div class="header bwHeadLight">
                <?php
                $this->widget('cms.extensions.customPagers.CustomPagerPNCounter', array(
                    'id' => 'statistics-grid-pagination',
                    'itemCount' => $total,
                    'pageSize' => $pageSize,
                    'nextPageLabel' => "<span class='icon icon-arrow-right-narrow'></span>",
                    'prevPageLabel' => "<span class='icon icon-arrow-left-narrow'></span>",
                    'htmlOptions' => array('class' => 'pagination-pn pnDefault pnS floatR')
                ));
                ?>
                <div class="clear"></div>
            </div>
            <div class="grbar clearfix">
                
                
            </div>
            <?php

            $pre_html = '<table class="product-items">
                <thead>
                   
                    <th class="grid_left">' . t('site', 'Offerta') . '</th>
                    <th>' . t('site', 'Visits') . '</th>
                </thead>
                <tbody>';
            $post_html = '</tbody></table>';

            $this->widget('cms.extensions.customListsView.PlainCListView', array(
                'id' => 'statistics-grid',
                'htmlOptions' => array('class' => 'grid grDefault clearfix'),
                'dataProvider' => $dataProvider,
                'itemView' => 'common.blocks.statistics._item',
                'cssFile' => false,
                'preItemsTag' => $pre_html,
                'postItemsTag' => $post_html,
                'template' =>  "{items}{summary}{pager}",
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
                'enablePagination' => true,
            ));
            ?>
        </div>




</section>





