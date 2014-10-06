<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<section id="live-notifications" class="box_round_c grid_19 omega">
    <h1 class="dash"><?php echo t('site', 'Notifiche'); ?></h1>
    <div id="notifications-list" class="notifications-wrapper">
        <?php
        echo "<ul class='items'>";

        $fdates = array();
        foreach ($model as $m) {
            $fdates[] = $m->create_time;
        }

        foreach ($dates as $date) {
            echo "<li>";
            if (in_array($date, $fdates)) {
                echo "<div date=" . $date . " class='notification-date row'>" . date('j M', $date) . "</div>";
            }
            
            echo "<ul>";
            foreach ($model as $m) {
                if ($m->create_time == $date) {
                    echo "<li class='row clearfix'>";
                    echo "<div class='icon'><img src=".$m->generateIcon()." /></div>";
                    echo "<div class='info'>".$m->body."</div>";
                    echo "</li>";
                }
            }
            echo "</ul>";
            echo "</li>";
        }
        echo "</ul>";
        ?>
        <?php

        $this->widget('cms.extensions.infiniteScroll.IasPager', array(
            'currentPage' => $pages->getCurrentPage(),
            'htmlOptions' => array('class' => 'pager'),
            'itemCount' => $itemsCount,
            'pageSize' => $this->page_size,
            'rowSelector' => '.row',
            'listViewId' => 'notifications-list',
            'header' => '',
            'loader' => '<img src="' . $layout_asset . '/images/loader.gif"/>',
            'options' => array(
                'history' => false,
                //'triggerPageTreshold' => 2,
                'trigger' => t('site', 'Mostra altro'),
                //'onLoadItems' => "js:function(items){}",
                'onRenderComplete' => "js:function(items) {
                    // we check for duplicates and delete them if so
                    var dates = {};
                    $('#notifications-list .row').each(function(){
                        if ($(this).attr('date') !== undefined) {
                            var el = $(this).attr('date');
                            if (dates[el])
                                $(this).remove();
                            else
                                dates[el] = true;
                        }
                    });
                }",
            )
        ));
        ?>
    </div>
</section>
