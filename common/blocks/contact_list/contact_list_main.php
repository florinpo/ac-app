<section id="ys-contacts">
    <div class="box_round_c grid_19 omega">
        <h1><?php echo t('site', 'Messagi'); ?></h1>
        <?php
        $unreadInbox = Mailbox::countUnreadMsgs(user()->id, 'inbox');
        $counterInbox = $unreadInbox > 0 ? "<span class='counter' id='counter-inbox'>".$unreadInbox."</span>" : "";
        $unreadSpam = Mailbox::countUnreadMsgs(user()->id, 'spam');
        $counterSpam = $unreadSpam > 0 ? "<span class='counter' id='counter-spammed'>".$unreadSpam."</span>" : "";
        
        $this->widget('zii.widgets.CMenu', array(
            'htmlOptions' => array('class' => 'tabnav floatL'),
            'encodeLabel' => false, 
            'items' => array(
                array(
                    'label' => t('site', 'Inbox') . $counterInbox,
                    'url' => array('page/render', 'slug' => 'messages', 'folder' => 'inbox'),
                    'active' => false
                ),
                array(
                    'label' => t('site', 'Sent'),
                    'url' => array('page/render', 'slug' => 'messages', 'folder' => 'sent'),
                    'active' => false
                ),
                array(
                    'label' => t('site', 'Archived'),
                    'url' => array('page/render', 'slug' => 'messages', 'folder' => 'archived'),
                    'active' => false
                ),
                array(
                    'label' => t('site', 'Spam') . $counterSpam,
                    'url' => array('page/render', 'slug' => 'messages', 'folder' => 'spam'),
                    'active' => false
                ),
                array(
                    'label' => t('site', 'Trash'),
                    'url' => array('page/render', 'slug' => 'messages', 'folder' => 'trash'),
                    'active' => false
                )
            )
        ));
        ?>
        
        <?php
        $this->widget('zii.widgets.CMenu', array(
            'htmlOptions' => array('class' => 'tabnav floatR'),
            'encodeLabel' => false,
            'items' => array(
                array(
                    'label' => "<span class='icon icon-edit'></span>".t('site', 'Compose'),
                    'url' => array('page/render', 'slug' => 'messages', 'action' => 'compose'),
                    'active' => false
                ),
                array(
                    'label' => "<span class='icon icon-address-book'></span>".t('site', 'Contact list'),
                    'url' => array('page/render', 'slug' => 'contact-list'),
                    'active' => true
                )
            )
        ));
        ?>
        <div class="clear"></div>
        <div class="tabnav-body">
            <div class="contact-list-mailbox clearfix">

                <?php if ($total > 0): ?>
                    <div class="contact-list-wrapper">
                        <?php
                        // render the listview widget
                        $this->render('common.blocks.contact_list._list_view', array('dataProvider' => $dataProvider));
                        ?>
                    </div>

                    <div class="cview" id="container-view">
                        <div class="loader loader-label-30"><span class='loader-txt'><?php echo t('site', 'Caricamento'); ?></span></div>

                        <div class="default-view">
                            <div class="header">
                                <h2><?php echo t('site', 'You have :count contacts', array(':count' => $total)) ?></h2>
                            </div>
                            <div class="content">
                                <p><?php echo t('site', 'Choose a contact to view.') ?></p>
                            </div>
                        </div>

                        <div class="contact-view">
                            <div class="actions">
                                <?php echo CHtml::link("<span class='icon icon-remove'></span><span>" . t('site', 'Delete contact') . "</span>", 'javascript:void(0)', array('class' => 'cview-btn', 'id' => 'delete')) ?>
                                <?php echo CHtml::link("<span class='icon icon-mail'></span><span>" . t('site', 'Write a message') . "</span>", 'javascript:void(0)', array('class' => 'cview-btn', 'id' => 'compose')) ?>
                            </div>
                        </div>

                        <div class="multiple-selected">
                            <div class="header">
                                <h2><span class="counter"></span><?php echo t('site', ' contacts selected'); ?></h2>
                            </div>
                            <div class="actions clearfix">
                                <div class="box-action clearfix">
                                    <div class="col-l">
                                        <span class="icon icon-mail"></span>
                                    </div>
                                    <div class="col-r">
                                        <h3><?php echo t('site', 'Email') ?></h3>
                                        <p><?php echo t('site', 'Invia un messagio a contatti selezionati.') ?></p>
                                        <?php echo CHtml::link(t('site', 'Scrivi messagio'), 'javascript:void(0)', array('class' => 'cview-btn btn buttonM bDefault', 'id' => 'compose')) ?>
                                    </div>
                                </div>

                                <div class="box-action clearfix">
                                    <div class="col-l">
                                        <span class="icon icon-remove"></span>
                                    </div>
                                    <div class="col-r">
                                        <h3><?php echo t('site', 'Elimina') ?></h3>
                                        <p><?php echo t('site', 'Cancela contatti.') ?></p>
                                        <?php echo CHtml::link(t('site', 'Elimina contatti'), 'javascript:void(0)', array('class' => 'cview-btn btn buttonM bDefault', 'id' => 'delete')) ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty">
                        <?php echo t('site', 'No contact found. Please click here to learn how to add contacts...') ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
</section>