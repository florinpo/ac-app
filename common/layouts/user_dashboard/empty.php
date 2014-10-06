<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.user_dashboard.assets'));
//Render Widget for Content Region
$this->widget('BlockRenderWidget', array('page' => $page, 'region' => '1', 'layout_asset' => $layout_asset));
?>