
<div class="box-account">
    <h3 class="box-header"><?php echo t('site', 'Messages'); ?></h3>
    <ul class="menu-account">
        <li>
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

        <li>
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

<?php if ($user->user_type == ConstantDefine::USER_COMPANY): ?>
<div class="box-account">
    <h3 class="box-header"><?php echo t('site', 'Products/Services'); ?></h3>
    <ul class="menu-account">
        <li>
            <?php echo CHtml::link(t('site', 'Add Product'), array('page/render', 'slug' => 'product-addedit-sale')); ?>
        </li>
        
        <li>
            <?php echo CHtml::link(t('site', 'List Product'), array('page/render', 'slug' => 'product-list-sale')); ?>
        </li>
        
    </ul>
</div>
<?php endif; ?>

<div class="box-account">
    <h3 class="box-header"><?php echo Yii::t('FrontendMessage', 'Account Information'); ?></h3>
    <ul class="menu-account">
        <?php if ($user->user_type == ConstantDefine::USER_NORMAL): ?>
            <li>
                <?php
                echo CHtml::link(t('site', 'Profile'), array('page/render', 'slug' => 'profile'));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Settings'), array('page/render', 'slug' => 'settings'));
                ?>  
            </li>
        <?php else: ?>
            <li>
                <?php
                echo CHtml::link(t('site', 'Member Profile'), array('page/render', 'slug' => 'member-profile'));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Company Profile'), array('page/render', 'slug' => 'company-profile'));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Settings'), array('page/render', 'slug' => 'company-settings'));
                ?>
            </li>
            <?php if($user->has_membership==1): ?>
            <li>
                <?php
                echo CHtml::link(t('site', 'Manage store'), array('page/render', 'slug' => 'manage-store'));
                ?>
            </li>
            <?php endif; ?>
            
        <?php endif; ?>
        <li>
            <?php
            echo CHtml::link(t('site', 'Change Password'), array('page/render', 'slug' => 'change-password'));
            ?> 
        </li>
    </ul>
</div>