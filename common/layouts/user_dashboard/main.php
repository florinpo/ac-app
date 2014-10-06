
<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.user_dashboard.assets'));
?>
<?php $this->renderPartial('common.layouts.default.header', array('page' => $page, 'layout_asset' => $layout_asset)); ?>      
<body>		
    <div id="page">
        <div id="header">
            <div class="container">
                <h1 class="logo-info"><?php echo settings()->get('general', 'site_name'); ?></h1>
            </div>									
            <?php
            //Render Widget for Header Region
            $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '0', 'layout_asset' => $layout_asset));
            ?>													
        </div><!-- header -->

        <div class="container">
            <div class="top_bar">
            <ul>
                <?php
                // Show the register or login button					
                if (user()->isGuest) {
                    $headerMenu = array(
                        bu().'/sign-in' => t('site', 'Login'),
                    );
                } else {
                    $headerMenu = array(
                        'site/logout' => t('site', 'Logout'),
                    );
                }
                ?>
                <li>
                    <?php
                    if (!user()->isGuest) {
                        echo t('site', 'Welcome <strong>{username}</strong>,', array(
                            '{username}' =>User::model()->findByPk(user()->id)->full_name));
                    }
                    ?>
                </li>
                <?php foreach ($headerMenu as $key => $value): ?>
                    <li>
                        <a href='<?php echo $this->createUrl($key); ?>'><?php echo $value; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
            <div id="content">
                <?php
                //Render Widget for Content Region			
                $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '1', 'layout_asset' => $layout_asset));
                ?>
            </div>
            <div id="sidebar">
                <?php
                //Render Widget for Content Region			
                $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '2', 'layout_asset' => $layout_asset));
                ?>
            </div>
            <div class="clear"></div>				

        </div>



    </div><!-- page -->					

    <div id="footer">
        &copy; Copyright 2012 
        <?php
        //Render Widget for Footer Script
        $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '4', 'layout_asset' => $layout_asset));
        ?>		
    </div><!-- footer -->

</body>
</html>