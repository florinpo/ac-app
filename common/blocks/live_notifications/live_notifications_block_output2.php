<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<div id="notification-list">
    <?php
    if (count($dates) > 0) {
        echo "<ul class='items'>";
        foreach ($dates as $date) {
            $notifications = Notification::model()->findAll(array(
                'condition' => 'user_id=:userId AND create_time=:createTime',
                'params' => array(':userId' => user()->id, ':createTime' => $date),
                'order' => 'create_time DESC')
            );
            if (count($notifications) > 0) {
                echo "<li>";
                echo "<div class='notification-date'>" . date('j M', $date) . "</div>";
                echo "<ul>";
                foreach ($notifications as $notification) {
                    echo "<li class='row'>";
                    echo $notification->body;
                    echo "</li>";
                }
                echo "</ul>";
                echo "</li>";
            }
        }
        echo "</ul>";
    }
    ?>
    <?php
    $this->widget('CLinkPager', array(
        'currentPage' => $pages->getCurrentPage(),
        'itemCount' => $itemsCount,
        'pageSize' => $this->page_size,
        'maxButtonCount' => 5,
        //'nextPageLabel'=>'My text >',
        'header' => '',
    ));



//    $this->widget('cms.extensions.infiniteScroll.IasPager', array(
//        'currentPage' => $pages->getCurrentPage(),
//        'itemCount' => $itemsCount,
//        'pageSize' => $this->page_size,
//        'rowSelector' => '.row',
//        'listViewId' => 'notification-list',
//        'header' => '',
//        'loader' => '<img src="' . $layout_asset . '/images/loader.gif"/>',
//        'options' => array(
//            'history' => false,
//            //'triggerPageTreshold' => 2,
//            'trigger' => t('site', 'Mostra altro'),
//        )
//    ));
    ?>
</div>
