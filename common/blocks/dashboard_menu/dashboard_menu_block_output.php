<div class="box_widget grid_5 alpha">
    <div class="header_grey_d">
        <h3><?php echo t('site', 'Messagi') ?></h3>
    </div>
    <div class="content_grey">
        <ul class="square">
            <li class="first">
                <?php
                if (PrivateMessage::model()->unreadMessages(user()->id) > 0) {
                    $link = t('site', 'Inbox') . '(' . PrivateMessage::model()->unreadMessages(user()->id) . ')';
                } else {
                    $link = t('site', 'Inbox');
                }
                echo CHtml::link($link, array('page/render', 'slug' => 'inbox'));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Sent'), array('page/render', 'slug' => 'outbox'));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Contact List'), array('page/render', 'slug' => 'contact-list'));
                ?>
            </li>
            <li>
                <?php
                if (PrivateMessage::model()->deletedMessages(user()->id) > 0) {
                    $link = t('site', 'Deleted') . '(' . PrivateMessage::model()->deletedMessages(user()->id) . ')';
                } else {
                    $link = t('site', 'Deleted');
                }
                echo CHtml::link($link, array('page/render', 'slug' => 'recyclebox'));
                ?>
            </li>

            <li class="last">
                <?php
                if (PrivateMessage::model()->spamMessages(user()->id) > 0) {
                    $link = t('site', 'Spam') . '(' . PrivateMessage::model()->spamMessages(user()->id) . ')';
                } else {
                    $link = t('site', 'Spam');
                }
                echo CHtml::link($link, array('page/render', 'slug' => 'spambox'));
                ?> 
            </li>
        </ul>
    </div>
</div>
<?php if ($user->user_type == ConstantDefine::USER_COMPANY): ?>
    <div class="box_widget grid_5 alpha">
        <div class="header_grey_d">
            <h3><?php echo t('site', 'Prodotti / Servizi') ?></h3>
        </div>
        <div class="content_grey">
            <ul class="square">
                <li class="first">
                    <?php echo CHtml::link(t('site', 'Add Product'), array('page/render', 'slug' => 'product-form')); ?>
                </li>

                <li class="last">
                    <?php echo CHtml::link(t('site', 'Manage Products'), array('page/render', 'slug' => 'products-manage')); ?>
                </li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="box_widget grid_5 alpha">
    <div class="header_grey_d">
        <h3><?php echo t('site', 'Informazioni conto') ?></h3>
    </div>
    <div class="content_grey">
        <ul class="square">
            <?php if ($user->user_type == ConstantDefine::USER_NORMAL): ?>
                <li class="first">
                    <?php
                    echo CHtml::link(t('site', 'Edit account'), array('page/render', 'slug' => 'user-edit-account'));
                    ?>
                </li>
                <li>
                    <?php
                    echo CHtml::link(t('site', 'Settings'), array('page/render', 'slug' => 'settings'));
                    ?>  
                </li>
            <?php else: ?>
                <li class="first">
                    <?php
                    echo CHtml::link(t('site', 'Edit account'), array('page/render', 'slug' => 'comp-edit-account'));
                    ?>
                </li>
                <li>
                    <?php
                    echo CHtml::link(t('site', 'Edit store'), array('page/render', 'slug' => 'edit-shop'));
                    ?>
                </li>
                <?php if ($user->has_membership == 1): ?>
                    <li>
                        <?php
                        echo CHtml::link(t('site', 'Manage store'), array('page/render', 'slug' => 'manage-store'));
                        ?>
                    </li>
                <?php endif; ?>
                    <li>
                    <?php
                    echo CHtml::link(t('site', 'Settings'), array('page/render', 'slug' => 'company-settings'));
                    ?>
                </li>

            <?php endif; ?>
            <li class="last">
                <?php
                echo CHtml::link(t('site', 'Change Password'), array('page/render', 'slug' => 'change-password'));
                ?> 
            </li>
        </ul>
    </div>
</div>