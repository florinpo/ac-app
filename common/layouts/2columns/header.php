<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="<?php echo ($page->allow_index) ? 'index' : 'noindex' ;?>, <?php echo ($page->allow_follow) ? 'follow' : 'nofollow' ;?>" />
    <meta name="author" content="GxcSolutions" />
    <meta name="copyright" content="GxcSolutions" />
   
    <link rel="shortcut icon" href="<?php echo $layout_asset; ?>/images/favicon.ico" type="image/x-icon" />               
    <link rel="stylesheet" href="<?php echo $layout_asset; ?>/css/screen.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="<?php echo $layout_asset; ?>/css/print.css" type="text/css" media="print" />
    <!--[if IE]><link rel="stylesheet" href="<?php echo $layout_asset; ?>/css/ie.css" type="text/css" media="screen, projection" /><![endif]-->

    <!-- custom stylesheet -->
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $layout_asset; ?>/css/custom.css" />
    

        
    <title><?php echo $this->pageTitle; ?></title>
        
    <?php    
        $cs=Yii::app()->clientScript;
                
        $cs->scriptMap=array(
            'jquery-ui.css'=>false,
        ); 
        $cs->registerCoreScript('jquery');        
        $cs->registerCoreScript('jquery.ui');
                   
	$cs->registerScriptFile( $layout_asset.'/js/custom.js?v=1');        
    ?>
   
</head>