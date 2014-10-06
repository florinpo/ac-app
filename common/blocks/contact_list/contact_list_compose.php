<div class="contact-list-mailbox clearfix">
    <?php if ($total > 0): ?>
        <div class="contact-list-wrapper">
            <?php
            // render the listview widget
            $this->render('common.blocks.contact_list._list_view', array('dataProvider' => $dataProvider));
            ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <?php echo t('site', 'No contact found. Please click here to learn how to add contacts...') ?>
        </div>
    <?php endif; ?>

</div>
