<?php ?>

<div class="dialog-content-wrapper">
    <p class="info"><?php echo t('site', 'Attenzione! Selezionare il settore, categoria e sotto-categoria, quindi premere il tasto "Select" e poi "Salva":') ?></p>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'product-category-form',
        'htmlOptions' => array('class' => 'categories-form'),
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validationDelay' => 100,
            'hideErrorMessage' => true,
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validationUrl' => bu() . '/ajax'
            )));
    ?>
    
    <div class="box-form clearfix">
        <div class="loading"></div>
        <div class="loader loader-label-30"><span class='loader-txt'><?php echo t('site', 'Caricamento'); ?></span></div>
        <div class="col">
            <label for="domain_id"><span class="icon icon-16 icon-uniF6BC"></span><?php echo t('site', 'Select domain:'); ?></label>
            <?php
            $domains = ProductSaleCategoryList::getDomains(false);
            $last_key_d = end(array_keys($domains));
            ?>
            <div class="items-wrapper">
                <?php if (count($domains) > 0): ?>
                    <ul class="cat-items" id="p-domains">
                        <?php foreach ($domains as $k => $domain): ?>
                            <?php $dom_class = ($k == $last_key_d) ? 'domain-item last' : 'domain-item'; ?>
                            <li class="domain-item clearfix">
                                <input id="d_<?php echo $k; ?>" class="c-domain s-radio" type="checkbox" name="domains[]" value="<?php echo $k; ?>" />
                            <span class="c-name"><?php echo $domain; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php echo $form->hiddenField($categoryForm, 'domain_id'); ?>
            <?php echo $form->error($categoryForm, 'domain_id'); ?>
        </div>
        <div class="col">
            <label for="category_id"><span class="icon icon-16 icon-uniF6BD"></span><?php echo t('site', 'Select category:'); ?></label>
            <div class="items-wrapper">
                <ul class="cat-items" id="p-categories"></ul>
            </div>
            <?php echo $form->hiddenField($categoryForm, 'category_id'); ?>
            <?php echo $form->error($categoryForm, 'category_id'); ?>
        </div>
        <div class="col last">
            <label for="subcategory_id"><span class="icon icon-16 icon-uniF6BE"></span><?php echo t('site', 'Select subcategory:'); ?></label>
            <div class="items-wrapper">
                <ul class="cat-items" id="p-subcategories"></ul>
            </div>
            <?php echo $form->hiddenField($categoryForm, 'subcategory_id'); ?>
            <?php echo $form->error($categoryForm, 'subcategory_id'); ?>
        </div>
    </div>

    <div class="box-form clearfix">
        <div class="actions clearfix">
            <?php echo $form->errorSummary($categoryForm, '', ''); ?>
            <?php
            echo CHtml::link("<span class='icon icon-circle-arrow-down'></span><span>"
                    . t('site', 'Seleziona') .
                    "</span>", 'javascript:void(0)', array('class' => 'buttonS btn bDefault wLb floatL', 'id' => 'select-category'));
            ?>
            <?php
            echo CHtml::link("<span class='icon icon-ok'></span><span>"
                    . t('site', 'OK, aggiungi i selezionati') .
                    "</span>", 'javascript:void(0)', array('class' => 'buttonL btn bGreen wLb floatR', 'id' => 'save-selected'));
            ?>
        </div>
    </div>

    <div class="box-form selected clearfix">
        <div class="col-selected">
            <label for="selected_categories"><span class="icon icon-16 icon-uniF6BF"></span><?php echo t('site', 'Hai scelto:'); ?></label>
            <div class="s-wrapper">
                
                <div class="empty">
                    <?php echo t('site', '0 categorie selezionati. Puoi selezionare ancora 3 categorie per prodotto.') ?>
                </div>
                
                <ul class="cat-selected" id="selected-items"></ul>
                
            </div>
            <?php
            echo $form->hiddenField($categoryForm, 'selected_categories', array('type' => "hidden", 'value' =>''));
            ?>
            <?php echo $form->error($categoryForm, 'selected_categories'); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>