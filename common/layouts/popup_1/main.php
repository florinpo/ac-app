<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<?php $this->renderPartial('common.layouts.wide.header', array('page' => $page, 'layout_asset' => $layout_asset)); ?>      
<body>
    <!-- begin container -->
    <div id="container">
        <!-- begin header -->
        <header id="header-popup">
            <!-- begin header top -->
            <div class="container_24">
                <section id="header-top" class="grid_24">
                    <!-- begin left-floated box -->
                    <div class="grid_12 alpha">
                        <h1 id="logo"><img src="<?php echo $layout_asset; ?>/images/logos/logo.png" alt="<?php echo settings()->get('general', 'site_name'); ?>"/></h1>
                    </div>
                    <!-- end left-floated box -->
                    <!-- begin right-floated box -->
                    <div class="grid_12 omega">
                           <a href="javascript:void(0);" class="close"><?php echo t('site', 'Chiudi finestra') ?></a>
                    </div>
                </section>
            </div>
            <!-- end header top -->    
            <?php
            //Render Widget for Header Region
            $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '0', 'layout_asset' => $layout_asset));
            ?>
        </header>
        <!-- end header -->
        <!-- begin content -->
        <section id="content_wrapper_popup">
            <div class="container_24">
                <!-- begin content -->
                <?php
                //Render Widget for Content Region			
                $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '1', 'layout_asset' => $layout_asset));
                ?>
                <!-- end content -->
            </div>
        </section>
        <!-- end content -->
        <!-- begin footer -->
        <footer id="footer">
            <?php
            //Render Widget for Footer Region
            $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '3', 'layout_asset' => $layout_asset));
            ?>
        </footer>
        <!-- end footer -->
    </div>
    <!-- end container -->
</body>
</html>