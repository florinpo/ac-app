<div class="box_widget grid_24">
    <div class="form-steps">
        <span class="selected"><?php echo t("site", "1. Scegli prodotto"); ?></span>
        <span><?php echo t("site", "2. Dati di fatturazione"); ?></span>
        <span><?php echo t("site", "3. Genera fattura"); ?></span>
    </div>
    <section id="premium-offer" class="grid_24">
        <div class="label-info grid_8">
            <header class="top">
                <div></div>
                <h2><?php echo t("site", "Alege un pachet premium member!"); ?></h2>
                <p><?php echo t("site", "Vei avea avantaje extraordinare."); ?></p>
            </header>
            <ul class="items">
                <li><?php echo t('site','Prodotti vendida') ?></li>
                <li><?php echo t('site','Annunci vendita/richiesta') ?></li>
            </ul>          
        </div>
        <div class="pricing-box grid_5">
            <header class="top">
                <h3 class="title">Premium 100</h2>
                <p class="price">
                    <span>69</span>
                    <sup>euro/anno</sup>
                </p>
            </header>
            <ul class="items">
                <li><strong>5</strong> Users</li>
                <li><strong>10</strong> Forms</li>
                <li><strong>20</strong> Reports</li>
            </ul>           
            <footer class="bottom">
                <?php echo CHtml::link('<span class="inner"><span class="text">'.t('site', 'Ordina adesso').'</span></span>', array('page/render', 'slug'=>'order', 'type'=>'premium', 'item-id'=>'22'), array('class'=>'btn-n black')); ?>
            </footer>
        </div>
        <div class="pricing-box grid_5">
            <header class="top">
                <h3 class="title">Premium 1500</h2>
                <p class="price">
                    <span>119</span>
                    <sup>euro/anno</sup>
                </p>
            </header>
            <ul class="items">
                <li><strong>5</strong> Users</li>
                <li><strong>10</strong> Forms</li>
                <li><strong>20</strong> Reports</li>
            </ul>           
            <footer class="bottom">
                <?php echo CHtml::link('<span class="inner"><span class="text">'.t('site', 'Ordina adesso').'</span></span>', array('page/render', 'slug'=>'order', 'type'=>'premium', 'item-id'=>'24'), array('class'=>'btn-n black')); ?>
            </footer>
        </div>
        <div class="pricing-box grid_5">
            <header class="top">
                <h3 class="title">Premium 2500</h2>
                <p class="price">
                    <span>169</span>
                    <sup>euro/anno</sup>
                </p>
            </header>
            <ul class="items">
                <li><strong>5</strong> Users</li>
                <li><strong>10</strong> Forms</li>
                <li><strong>20</strong> Reports</li>
            </ul>           
            <footer class="bottom">
                <?php echo CHtml::link('<span class="inner"><span class="text">'.t('site', 'Ordina adesso').'</span></span>', array('page/render', 'slug'=>'order', 'type'=>'premium', 'item-id'=>'3'), array('class'=>'btn-n black')); ?>
            </footer>
        </div>
    </section>
</div>