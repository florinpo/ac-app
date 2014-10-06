<?php
$arr = array(1, 2, 3);
foreach($arr as $number) {
  if($number == 2) {
    continue;
  }
  print $number." ";
}
?>




<?php
$date_from_min = '29-01-2011'; // suppose this will be the date since the store is available
$date_from_max = date('d-m-Y', time() - 6 * 24 * 3600); // today - 7 days

$date_to_min = date('d-m-Y', strtotime($date_from_max) + 6 * 24 * 3600); // date_from_max + 7 days

$date_to_max = date('d-m-Y', time());
?>
<section id="statistics">
    <div class="box_round_c grid_19 holder">

        <h1><?php echo t('site', 'Statistics'); ?></h1>

        <div class="actions-bar">
            <div class="grid_5">
                <label><?php echo t('site', 'Start date:'); ?></label>
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
                            
                            $("#date_to").datepicker( "option", "minDate", $.statistics.getEndDateMin(date_from));
                            $(".ds-btn").removeClass("active");
                            $.statistics.ajaxUpdateGrid(date_from, date_to);
                }',
                    )
                ));
                ?>
            </div>
            <div class="grid_5">
                <label><?php echo t('site', 'End date:'); ?></label>
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
                            //$("#date_from").datepicker( "option", "minDate", $.statistics.getEndDateMin(date_to));
                             $(".ds-btn").removeClass("active");
                            $.statistics.ajaxUpdateGrid(date_from, date_to);
    
                    }',
                    )
                ));
                ?>
            </div>

            <div class="grid_6 floatR">
                <div class="btn-group floatR">
                    <ul class="toolbar">
                        <li><?php echo CHtml::link(t('site', 'Week'), 'javascript:void(0)', array('class' => 'buttonS bDefault wLb active ds-btn', 'id' => 'week')); ?></li>
                        <li><?php echo CHtml::link(t('site', 'Month'), 'javascript:void(0)', array('class' => 'buttonS bDefault wLb ds-btn', 'id' => 'month')); ?></li>
                        <li><?php echo CHtml::link(t('site', 'Quarter'), 'javascript:void(0)', array('class' => 'buttonS bDefault wLb ds-btn', 'id' => 'quarter')); ?></li>
                    </ul>
                </div>

            </div>
            <div class="clear"></div>
        </div>

        <div class="loading-wrapper"></div>
        <div class="loader-indicator loader-label-30">
            <span class="loader-txt">
                <?php echo t('site', 'Caricamento'); ?>
            </span>
        </div>

        <div class="chart-wrapper clearfix">
            <div id="m-choices" class="metric-options clearfix"></div>
            <div id="c-placeholder" class="chart chart-placeholder"></div>
        </div>
        
        <?php
        $this->render('common.blocks.statistics._grid', array(
            'dataProvider' => $dataProvider,
            'total' => $total,
            'pageSize' => $pageSize
        ));
        ?>


</section>





