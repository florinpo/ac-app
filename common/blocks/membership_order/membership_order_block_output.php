<div class="box_widget grid_24">
    <div class="form-steps">
        <span class="selected"><?php echo t("site", "1. Scegli prodotto"); ?></span>
        <span><?php echo t("site", "2. Dati di fatturazione"); ?></span>
        <span><?php echo t("site", "3. Genera fattura"); ?></span>
    </div>
    <section id="premium-offer" class="grid_24">
        <div class="label-info grid_8">
            <div class="box-tip box-tip-green">
                <div class="content">
                  <h2><?php echo t("site", "Alege un pachet premium member!"); ?></h2>
                  <p class="info"><?php echo t("site", "Vei avea avantaje extraordinare!"); ?></p>
                </div>
                <div class="box-tip-arrow-left box-tip-arrow">
                    <span class="box-tip-arrow-border"></span>
                    <span class="box-tip-arrow-collor"></span>
                </div>
            </div>
            <ul class="items">
                <li><?php echo t('site','Prodotti vendida') ?></li>
                <li><?php echo t('site','Annunci offerte/richiesta') ?></li>
                <li><?php echo t('site','Sconti') ?></li>
            </ul>
        </div>
        
        <div id="p_1500" class="pricing-box grid_5">
            <header class="top">
                <h3 class="title"><?php echo $p_100->title; ?></h2>
                    <p class="price">
                    <span><?php echo round2($p_100->price); ?></span>
                    <sup>euro/anno</sup>
                    </p>
            </header>
            <ul class="items">
                <li><strong><?php echo  $p_100->items_num; ?></strong><?php echo t('site', ' prodotti'); ?></li>
                <li><strong><?php echo  $p_100->items_num; ?></strong><?php echo t('site', ' annunci'); ?></li>
                <li><strong><?php echo  $p_100->items_num; ?></strong><?php echo t('site', ' articoli'); ?></li>
            </ul>           
            <footer class="bottom">
                <?php echo CHtml::link('<span class="inner"><span class="text">' . t('site', 'Ordina adesso') . '</span></span>', array('page/render', 'slug' => 'order', 'type' => 'premium', 'item-id' =>$p_100->id), array('class' => 'btn-n black')); ?>
            </footer>
        </div>
        
        <div id="p_1500" class="pricing-box grid_5">
            <header class="top">
                <h3 class="title"><?php echo $p_1500->title; ?></h2>
                    <p class="price">
                    <span><?php echo round2($p_1500->price); ?></span>
                    <sup>euro/anno</sup>
                    </p>
            </header>
            <ul class="items">
                <li><strong><?php echo  $p_1500->items_num; ?></strong><?php echo t('site', ' prodotti'); ?></li>
                <li><strong><?php echo  $p_1500->items_num; ?></strong><?php echo t('site', ' annunci'); ?></li>
                <li><strong><?php echo  $p_1500->items_num; ?></strong><?php echo t('site', ' articoli'); ?></li>
            </ul>           
            <footer class="bottom">
                <?php echo CHtml::link('<span class="inner"><span class="text">' . t('site', 'Ordina adesso') . '</span></span>', array('page/render', 'slug' => 'order', 'type' => 'premium', 'item-id' =>$p_1500->id), array('class' => 'btn-n black')); ?>
            </footer>
        </div>
        
        <div id="p_2500" class="pricing-box grid_5">
            <header class="top">
                <h3 class="title"><?php echo $p_2500->title; ?></h2>
                    <p class="price">
                    <span><?php echo round2($p_2500->price); ?></span>
                    <sup>euro/anno</sup>
                    </p>
            </header>
            <ul class="items">
                <li><strong><?php echo  $p_2500->items_num; ?></strong><?php echo t('site', ' prodotti'); ?></li>
                <li><strong><?php echo  $p_2500->items_num; ?></strong><?php echo t('site', ' annunci'); ?></li>
                <li><strong><?php echo  $p_2500->items_num; ?></strong><?php echo t('site', ' articoli'); ?></li>
            </ul>           
            <footer class="bottom">
                <?php echo CHtml::link('<span class="inner"><span class="text">' . t('site', 'Ordina adesso') . '</span></span>', array('page/render', 'slug' => 'order', 'type' => 'premium', 'item-id' =>$p_2500->id), array('class' => 'btn-n black')); ?>
            </footer>
        </div>
    </section>
</div>