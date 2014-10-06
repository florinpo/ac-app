<div class="box_widget grid_24 alpha">
    <div class="form-steps">
        <span><?php echo t("site", "1. Scegli prodotto"); ?></span>
        <span><?php echo t("site", "2. Dati di fatturazione"); ?></span>
        <span class="selected"><?php echo t("site", "3. Genera fattura"); ?></span>
    </div>
    <?php $this->render('cmswidgets.views.notification_frontend'); ?>
</div>