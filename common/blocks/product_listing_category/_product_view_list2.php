<?php
$class = ($index % 2 == 0) ? "first" : "last";
$class .= ($data->shop->company->has_membership) ? " premium" : "";
?>
<li class="clearfix <?php echo $class; ?>">
<article>
    <div class="thumbnail">
        <a href="#"><?php echo $data->selectedImage(180); ?></a>
    </div>
    <div class="data-wrap">
        <h2><?php echo CHtml::link($data->name, '#'); ?></h2>
        <div class="description"><?php echo truncate($data->description, $this->wordsLimit); ?></div>

        <div class="op_main clearfix">
            <span class="c-name">
                <?php echo t('site', 'Offerto da: ') . '<a href="#">' . $data->shop->company->cprofile->companyname . '</a>'; ?>
                <?php if ($data->shop->company->has_membership): ?>
                    <?php echo CHtml::link(t('site', 'Membro Premium'), 'javascript:void(0)', array('class' => 'premium')) ?>
                <?php endif; ?>
            </span>
        </div>
    </div>
    <div class="actions">
        <div class="price">
            <?php if ($data->price > 0 && $data->discount_price > 0): ?>
                <div class="old-p">
                    <span class="currency">&euro;</span>
                    <span class="data"><?php echo $data->price; ?></span>
                </div>
                <div class="regular">
                    <span class="currency">&euro;</span>
                    <span class="data"><?php echo $data->discount_price; ?></span>
                </div>

            <?php elseif ($data->price > 0 && $data->discount_price==0): ?>
                <div class="regular">
                    <span class="currency">&euro;</span>
                    <span class="data"><?php echo $data->price; ?></span>
                </div>
            <?php elseif ($data->price == 0 && $data->discount_price==0): ?>
                <span class="data-empty"><?php echo t('site', '') ?></span>
            <?php endif; ?>
        </div>
        <!-- begin item-selectbox-form -->
        <?php echo CHtml::beginForm('', 'post', array('id' => 'item-selectbox-form-' . $data->id, 'class' => 'item-selectbox-form')); ?>
        <?php
        echo CHtml::dropDownList('item-options', '', array(1 => t('site', 'Adauga la favorite'), 2 => t('site', 'Vezi detalii'), 3 => t('site', 'Manda messagio')), array(
            'prompt' => 'Select option', 'id' => 'item-options'));
        ?>
        <?php echo CHtml::endForm(); ?>
        <!-- end item-selectbox-form -->
    </div>
</article>
</li>