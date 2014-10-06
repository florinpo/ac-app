<!DOCTYPE HTML>
<!--[if IE 7]> <html class="ie7 lte-ie8 lte-ie9 no-js"> <![endif]-->
<!--[if IE 8]> <html class="ie8 lte-ie8 lte-ie9 no-js"> <![endif]-->
<!--[if IE 9]> <html class="lte-ie9 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="it" class="no-js">
    <!--<![endif]-->
    <head>
        <!-- begin meta -->
        <meta charset="utf-8">
        <meta name="robots" content="<?php echo $page['allow_index'] ? 'index' : 'noindex'; ?>, 
              <?php echo $page['allow_follow'] ? 'follow' : 'nofollow'; ?>" />
        <link href="<?php echo $layout_asset; ?>/style.css" type="text/css" rel="stylesheet">
        <link href="<?php echo $layout_asset; ?>/images/favicon2.ico" type="image/x-icon" rel="shortcut icon">
        <?php
        $cs = Yii::app()->clientScript;
        $cs->scriptMap = array(
            //'jquery.js'=>"//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js",            
            'jquery-ui.js'=>false,
            'jquery-ui.min.js'=>$layout_asset . '/js/jquery-ui.min.js',
            'jquery-ui.min.css' => false,
            'jquery-ui.css' => false
        );
        
        $cs->registerScriptFile($layout_asset . '/js/modernizr.custom.js');
        $cs->registerScriptFile($layout_asset . '/js/jquery-ui.min.js');
        $cs->registerScriptFile($layout_asset . '/js/bootstrap/bootstrap-dropdown.js', CClientScript::POS_END);
        $cs->registerScriptFile($layout_asset . '/js/bootstrap/bootstrap-checkbox.js', CClientScript::POS_END);
        //$cs->registerScriptFile($layout_asset . '/js/plugins/jquery.selectbox-0.2.js', CClientScript::POS_END);
        $cs->registerScriptFile($layout_asset . '/js/custom.js?v=1', CClientScript::POS_END);
        ?>
       
        <title><?php echo $this->pageTitle; ?></title>
        <?php
        //Render Widget for Header Script
        $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '5', 'layout_asset' => $layout_asset));
        ?>
    </head>