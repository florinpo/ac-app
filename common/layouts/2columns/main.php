<?php
if (YII_DEBUG)
    $layout_asset = Yii::app()->assetManager->publish(Yii::getPathOfAlias('common.front_layouts.2columns.assets'), false, -1, true);
else
    $layout_asset = Yii::app()->assetManager->publish(Yii::getPathOfAlias('common.front_layouts.2columns.assets'), false, -1, false);
?>
<?php $this->renderPartial('common.front_layouts.2columns.header', array('page' => $page, 'layout_asset' => $layout_asset)); ?>      
<body>

    <div class="container" id="container">
        <div class="top_bar">
            <ul>
                <?php
                // Show the register or login button					
                if (user()->isGuest) {
                    $headerMenu = array(
                        //Yii::app()->request->baseUrl.'/sign-in' => Yii::t('global', 'Register'),
                        '/sign-in' => Yii::t('global', 'Login'),
                    );
                } else {
                    $headerMenu = array(
                        //Yii::app()->request->baseUrl.'/logout' => Yii::t('global', 'Register'),
                        'site/logout' => Yii::t('global', 'Logout'),
                    );
                }
                ?>


                <li>
                    <?php
                    if (!user()->isGuest) {
                        echo Yii::t('global', 'Welcome <strong>{username}</strong>,', array(
                            '{username}' => (User::model()->findByPk(user()->id)) ? User::model()->findByPk(user()->id)->display_name : 'Guest'));
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

        <div class="span-24" id="content">
            <div class="introduce wide">
                <?php
                //Render Widget for Header Region
                $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '0', 'layout_asset' => $layout_asset));
                ?>
            </div>	

            <div class="inner_content">
                <div class="sidebar-l">
                    <?php
                    //Render Widget for Content Region
                    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '2', 'layout_asset' => $layout_asset));
                    ?>
                </div>
                <div class="info wide">	
                    <?php
                    //Render Widget for Content Region
                    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '1', 'layout_asset' => $layout_asset));
                    ?>
                </div>
            </div>
            <div class="clear"></div>

            <div class="footer_content">					
                <?php
                //Render Widget for Footer Region
                $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '3', 'layout_asset' => $layout_asset));
                ?>
            </div>
        </div>	
    </div>	
    <?php
//Render Widget for Footer Script
    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '5', 'layout_asset' => $layout_asset));
    ?>
</body>
</html>