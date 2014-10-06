<article>
    <div class="thumbnail">
        <?php
        echo CHtml::link($product->selectedImage(180), array(
            'site/store',
            'username' => $company->username,
            'page' => 'vendita',
            'prod_id' => $product->id,
            'prod_slug' => $product->slug
        ));
        ?>
    </div>
    <div class="data-wrap">
        <h2><?php
        echo CHtml::link($product->name, array(
            'site/store',
            'username' => $company->username,
            'page' => 'vendita',
            'prod_id' => $product->id,
            'prod_slug' => $product->slug
        ));
        ?></h2>
        <div class="description"><?php echo truncate($product->description, $this->wordsLimit); ?></div>

        <div class="op_main clearfix">
            <span class="c-name">
                <?php
                echo t('site', 'Offerto da: ') . CHtml::link($cprofile->companyname, array('site/store',
                    'username' => $company->username,
                    'page' => 'store-view')
                );
                ?>
                <?php if ($cmembership): ?>
                    <?php echo CHtml::link(t('site', 'Membro Premium'), 'javascript:void(0)', array('class' => 'premium')) ?>
                <?php endif; ?>
            </span>
        </div>
        <div class="bottom-bar clearfix">
            <div class="price">
                <?php if ($product->price > 0 && $product->discount_price > 0): ?>
                    <div class="old">
                        <span class="currency">&euro;</span>
                        <span class="data"><?php echo $product->price; ?></span>
                    </div>
                    <div class="regular">
                        <span class="currency">&euro;</span>
                        <span class="data"><?php echo $product->discount_price; ?></span>
                    </div>

                <?php elseif ($product->price > 0 && $product->discount_price == 0): ?>
                    <div class="regular">
                        <span class="currency">&euro;</span>
                        <span class="data"><?php echo $product->price; ?></span>
                    </div>
                <?php elseif ($product->price == 0 && $product->discount_price == 0): ?>
                    <span class="data-empty"><?php echo t('site', '- prezzo non specificato') ?></span>
                <?php endif; ?>
            </div>
            <?php if ($product->price > 0 && $product->discount_price > 0): ?>
                <div class="time-left">
                    <span>
                        <?php echo floor(($product->expire_time - time()) / (60 * 60 * 24)) . ' ' . t('site', 'giorni rimanenti'); ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- begin item-selectbox-form -->
            <?php echo CHtml::beginForm('', 'post', array('id' => 'item-selectbox-form-' . $product->id, 'class' => 'item-selectbox-form')); ?>
            <?php
            echo CHtml::dropDownList('item-options', '', array(1 => t('site', 'Adauga la favorite'), 2 => t('site', 'Vezi detalii'), 3 => t('site', 'Manda messagio')), array(
                'prompt' => 'Select option', 'id' => 'item-options'));
            ?>
            <?php echo CHtml::endForm(); ?>
            <!-- end item-selectbox-form -->
        </div>
    </div>

</article>
