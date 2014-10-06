<div class="box_widget grid_24 alpha">
    <div class="form-steps">
        <span><?php echo t("site", "1. Scegli prodotto"); ?></span>
        <span class="selected"><?php echo t("site", "2. Dati di fatturazione"); ?></span>
        <span><?php echo t("site", "3. Genera fattura"); ?></span>
    </div>
    <div class="form-wrapper-wide">
        <p class="info"><?php echo t('site', 'Va rugam ca datele firmei trecute in comanda sa fie corecte si complete, conforme cu realitatea, intrucat se vor regasi pe factura si contract.') ?></p>
        <?php
        $this->widget('cms.extensions.tooltipster.tooltipster', array(
            'identifier' => '.tooltip',
            'options' => array(
                'position' => 'right',
                'trigger' => 'click',
                'speed' => '150',
                'theme' => '.tooltipster-yellow'
                ))
        );
        ?>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'proforma-generate-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'htmlOptions' => array('autocomplete' => 'off', 'class' => 'payment-info-form'),
            'clientOptions' => array(
                'afterValidate' => 'js:function(form, data, hasError){
                if(hasError){
                    if($(".error input").attr("type") == "hidden") {
                      $(window).scrollTop($("#PaymentInfoForm_item").parent().parent().offset().top);
                    } else {
                      $(".error:not(.pitem):first").find("input").focus();
                    }
                }
                if($().tooltipster){
                    $("a.tooltip").tooltipster("hide");
                };
                return true;
             }',
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'validateOnType' => false,
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
            ),
                ));
        ?>
        <div class="box-form">
            <div class="row clearfix first pitem">
                <label for="item"><?php echo t('site', 'Hai scelto:'); ?></label>
                <div class="purchased-item">
                    <?php if (isset($item_order) && $item_order != null): ?>
                        <div class="i-row">
                            <span class="title"><?php echo $item_order->title; ?></span>
                            <span class="price"><?php echo $item_order->price . t('site', ' Euro'); ?></span>
                            <span class="info"><?php echo t('site', '(Il prezo non include IVA)'); ?></span>
                        </div>
                    <?php else: ?>
                        <span class="empty"><?php echo t('site', '- Nissun prodotto selectato'); ?></span>
                    <?php endif; ?>
                </div>

                <?php echo $form->hiddenField($model, 'item', array('type' => "hidden")); ?>
                <?php echo $form->error($model, 'item'); ?>
            </div>
        </div>
        <div class="box-form">
            <h3 class="f-headline"><?php echo t('site', 'Dati rappresentante:') ?></h3>
            <div class="row clearfix first">
                <label for="first_name" class="required"><?php echo t('site', 'First name:'); ?></label>
                <?php echo $form->textField($model, 'first_name', array('autoComplete' => 'off')); ?>
                <?php echo $form->error($model, 'first_name'); ?> 
            </div>
            <div class="row clearfix">
                <label for="last_name" class="required"><?php echo t('site', 'Last name:'); ?></label>
                <?php echo $form->textField($model, 'last_name', array('autoComplete' => 'off')); ?>
                <?php echo $form->error($model, 'last_name'); ?>
            </div>
            <div class="row clearfix">
                <label for="email" class="required"><?php echo t('site', 'E-mail:'); ?></label>
                <?php echo $form->textField($model, 'email', array()); ?>
                <?php echo $form->error($model, 'email'); ?>
            </div>
            <div class="row clearfix">
                <label for="company_position" class="required"><?php echo t("site", "Ruolo nell'azienda"); ?></label>
                <?php
                echo $form->dropDownList($model, 'company_position', array('1' => t('site', 'Director'),
                    '2' => t('site', 'General Manager'),
                    '3' => t('site', 'Company Owner'),
                    '4' => t('site', 'Sales'),
                    '5' => t('site', 'Marketing'),
                    '6' => t('site', 'Administration'),
                    '7' => t('site', 'Other')), array('empty' => ''));
                ?>
                <?php echo $form->error($model, 'company_position'); ?>
            </div>
        </div>
        <div class="box-form">
            <h3 class="f-headline"><?php echo t('site', 'Residenza:') ?></h3>
            <div class="row clearfix">
                <label for="region_id" class="required"><?php echo t('site', 'Regione'); ?></label>    
                <?php
                echo $form->dropDownList($model, 'region_id', Province::getRegion(), array(
                    'ajax' => array(
                        'type' => 'POST', //request type
                        'url' => app()->createUrl('site/provincefromregion'),
                        'data' => array('region_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                        'update' => '#' . CHtml::activeId($model, 'province_id'),
                        ))
                );
                ?>
                <?php echo $form->error($model, 'region_id'); ?>
            </div>

            <?php $get_province_id = isset($_GET['province_id']) ? (int) ($_GET['province_id']) : null ?>
            <?php if ($get_province_id === null) : ?>
                <div class="row clearfix">
                    <label for="region_id" class="required"><?php echo t('site', 'Provincia'); ?></label>	 
                    <?php echo $form->dropDownList($model, 'province_id', Province::getProvinceFromRegion($model->region_id, false), array('options' => array($model->province_id => array('selected' => true)))); ?>
                    <?php echo $form->error($model, 'province_id'); ?>
                </div>
            <?php else : ?>
                <?php echo $form->hiddenField($model, 'province_id', array('value' => $get_province_id)); ?>
            <?php endif; ?>

            <div class="row clearfix">
                <label for="location" class="required"><?php echo t('site', 'Comune:'); ?></label>
                <?php echo $form->textField($model, 'location'); ?>
                <?php echo $form->error($model, 'location'); ?>
            </div>

            <div class="row clearfix">
                <label for="adress" class="required"><?php echo t('site', 'Via/Piazza:'); ?></label>
                <?php echo $form->textField($model, 'adress'); ?>
                <?php echo $form->error($model, 'adress'); ?>
            </div>

            <div class="row clearfix">
                <label for="postal_code" class="required"><?php echo t('site', 'Cap:'); ?></label>
                <?php echo $form->textField($model, 'postal_code'); ?>
                <?php echo $form->error($model, 'postal_code'); ?>
            </div>
        </div>

        <div class="box-form">
            <h3 class="f-headline"><?php echo t('site', 'Dati azienda:') ?></h3>
            <div class="row clearfix">
                <label for="company_name" class="required"><?php echo t('site', 'Nome azienda:'); ?></label>
                <?php echo $form->textField($model, 'company_name', array('autoComplete' => 'off')); ?>
                <?php echo $form->error($model, 'company_name'); ?>
            </div>
            <div class="row clearfix">
                <label for="vat_code" class="required"><?php echo t('site', 'Partida IVA:'); ?></label>
                <?php echo $form->textField($model, 'vat_code'); ?>
                <?php echo $form->error($model, 'vat_code'); ?>
            </div>
            <div class="row clearfix">
                <label for="bank_name" class="required"><?php echo t('site', 'Nome banca:'); ?></label>
                <?php echo $form->textField($model, 'bank_name'); ?>
                <?php echo $form->error($model, 'bank_name'); ?>
            </div>
            <div class="row clearfix">
                <label for="bank_number" class="required"><?php echo t('site', 'Codice IBAN:'); ?></label>
                <?php echo $form->textField($model, 'bank_number'); ?>
                <?php echo $form->error($model, 'bank_number'); ?>
            </div>
            <div class="row clearfix">
                <label for="phone" class="required"><?php echo t('site', 'Telefono:'); ?></label>
                <?php echo $form->textField($model, 'phone'); ?>
                <?php echo $form->error($model, 'phone'); ?>
            </div>
            <div class="row clearfix">
                <label for="mobile"><?php echo t('site', 'Celulare:'); ?></label>
                <?php echo $form->textField($model, 'mobile', array('autoComplete' => 'off')); ?>
                <?php echo $form->error($model, 'mobile'); ?>
            </div>
            <div class="row clearfix">
                <label for="fax"><?php echo t('site', 'Fax:'); ?></label>
                <?php echo $form->textField($model, 'fax'); ?>
                <?php echo $form->error($model, 'fax'); ?>
            </div>
        </div>
        <div class="box-form-a">
            <div class="actions">
                <?php echo CHtml::submitButton(t('site', 'Genera fattura'), array('class' => 'btn-i-n i-green')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?> 
    </div>
</div>