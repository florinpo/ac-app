<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
//Render Widget for Content Region
$this->widget('BlockRenderWidget', array('page' => $page, 'region' => '3', 'layout_asset' => $layout_asset));
?>