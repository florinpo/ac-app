<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
?>
<?php $this->renderPartial('common.layouts.default.header', array('page' => $page, 'layout_asset' => $layout_asset)); ?>      
<body>
    <!-- begin container -->
    <div id="container">
         <!-- begin header -->
        <header id="header">
            <!-- begin header-wrap -->
            <div class="header-wrap">
                <div class="container_24">
                    <!-- begin left-floated box -->
                    <div class="logo-wrap grid_5 alpha">
                        <h1 id="logo"><a href="index.html"><img src="<?php echo $layout_asset; ?>/images/logos/logo.png" alt="<?php echo settings()->get('general', 'site_name'); ?>"></a></h1>
                    </div>
                    <!-- end left-floated box -->
                    <!-- begin right-floated box -->
                    <div class="grid_19 omega">
                        <!-- begin main-nav -->
                        <!-- begin navigation -->
                        <nav id="nav">
                            <?php
                            //Render Widget for Menu Region
                            $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '0', 'layout_asset' => $layout_asset));
                            ?>

                            <ul id="user-menu" class="user-menu">
                                <?php if (user()->isGuest): ?>
                                    <li class="login">
                                        <?php echo CHtml::link(t('site', 'Accedi al conto'), array('page/render', 'slug' => 'sign-in')); ?>
                                    </li>
                                    <li class="register">
                                        <?php echo CHtml::link(t('site', 'Registrati'), array('page/render', 'slug' => 'register')); ?>
                                    </li>
                                <?php else: ?>
                                    <?php $user = User::model()->findByPk(user()->id); ?>
                                    <li class="account">
                                        <div class="thumb">
                                            <?php
                                            $avatar = CHtml::image($layout_asset . '/images/icons/an-user.png', '')
                                            ?>
                                            <?php echo CHtml::link($avatar, array('page/render', 'slug' => 'dashboard'), array('class' => 'user-drop-down-trigger')); ?>
                                        </div>
                                        <div class="user-m">
                                            <?php echo CHtml::link(CHtml::encode($user->full_name), array('page/render', 'slug' => 'dashboard'), array('class' => 'user-drop-down-trigger um-trigger')); ?>
                                            <div class="user-drop-down-pannel">
                                                <ul>
                                                    <li><?php echo CHtml::link(t('site', 'Profilo'), array('page/render', 'slug' => 'profile')); ?></li>
                                                    <li><?php echo CHtml::link(t('site', 'Settings'), array('page/render', 'slug' => 'sign-in')); ?></li>
                                                    <li><?php echo CHtml::link(t('site', 'Esci'), array('site/logout')); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="message">
                                        <?php
                                        $class = 'active';
                                        ?>
                                    <span class="counter <?php echo $class; ?>">2</span>
                                    <?php echo CHtml::link(t('site', 'Inbox'), array('page/render', 'slug' => 'inbox'), array('class' => $class)); ?>
                                    </li>
                                    <li class="notification">
                                        <?php
                                        $class = 'active';
                                        ?>
                                    <span class="counter <?php echo $class; ?>">2</span>
                                    <?php
                                    echo CHtml::link(t('site', 'Inbox'), array('page/render', 'slug' => 'inbox'), array('class' => $class));
                                    ?>
                                    </li>
                                <?php endif; ?>
                                <li class="favorite">
                                    <?php echo CHtml::link(t('site', 'Favorite'), array('#')); ?>
                                </li>
                            </ul>
                        </nav>
                        <!-- end navigation -->
                        <!-- end main-nav -->
                    </div>
                    <?php
                    //Render Widget for Header Region
                    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '1', 'layout_asset' => $layout_asset));
                    ?>
                </div>
            </div>
            <!-- end header-wrap -->
        </header>
        <!-- end header -->
        <!-- begin content -->
        <section id="content_wrapper">
            <div class="container_24">
                <!-- begin top-content -->
                <div id="top-content" class="grid_24">
                    <?php
                    //Render Widget for Top Content Region			
                    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '2', 'layout_asset' => $layout_asset));
                    ?>
                </div>
                <!-- end top-content -->
                <!-- begin sidebar -->
                <div id="sidebar" class="grid_6 alpha">
                    <?php
                    //Render Widget for Content Region			
                    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '4', 'layout_asset' => $layout_asset));
                    ?>
                </div>
                <!-- end sidebar -->
                 <!-- begin content-info -->
                <div id="content-info" class="grid_18 omega">
                    <?php
                    //Render Widget for Content Region			
                    $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '3', 'layout_asset' => $layout_asset));
                    ?>
                </div>
                <!-- end content-info -->
            </div>
        </section>
        <!-- end content -->
        <!-- begin footer -->
        <footer id="footer">
            <?php
            //Render Widget for Footer Region
            $this->widget('BlockRenderWidget', array('page' => $page, 'region' => '5', 'layout_asset' => $layout_asset));
            ?>
        </footer>
        <!-- end footer -->
    </div>
    <!-- end container -->
</body>
</html>