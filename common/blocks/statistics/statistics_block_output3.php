
<?php
//var_dump($results);
///ar_dump($results);
//echo strtotime("2014-02-02 UTC") * 1000;


$date_from_min = '29-01-2011'; // suppose this will be the date since the store is available
$date_from_max = date('d-m-Y', time() - 7 * 24 * 3600); // today - 7 days

$date_to_min = date('d-m-Y', strtotime($date_from_max) + 7 * 24 * 3600); // date_from_max + 7 days
$date_to_max = date('d-m-Y', time());
?>
<section id="statistics">
    <div class="box_round_c grid_19">

        <h1><?php echo t('site', 'Messagi'); ?></h1>

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
                            
                            
                            $("#date_to").datepicker( "option", "minDate", $.statistics.getEndDateMin(date_from));
                            $.statistics.ajaxUpdateGrid(date_from, date_to);
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
                            $.statistics.ajaxUpdateGrid(date_from, date_to);
    
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

        <?php
        $this->render('common.blocks.statistics._grid', array(
            'dataProvider' => $dataProvider,
            'total' => $total,
            'pageSize' => $pageSize
        ));
        ?>

</section>





