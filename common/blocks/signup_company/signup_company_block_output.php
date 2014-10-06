
<div class="box_round_c grid_24">
    <h1 class="reg-headline"><?php echo t('site', 'Registrazione azienda a AffariClub.it') ?></h1>

    <div class="reg-step">
        <span class="selected"><?php echo t("site", "1. Creare conto"); ?></span>
        <span><?php echo t("site", "3. Verifica email"); ?></span>
        <span><?php echo t("site", "4. Registrazione finita"); ?></span>
    </div>
    
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
        'id' => 'user-company-register-form1',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'htmlOptions' => array('autocomplete' => 'off', 'class' => 'register-form'),
        'clientOptions' => array(
            'afterValidate' => 'js:function(form, data, hasError){
                if(hasError){
                    $(".error:first").find("input").focus();
                }
                if($().tooltipster){
                    $("a.tooltip").tooltipster("hide");
                };
                return true;
             }',
            'validateOnSubmit' => true,
            'validateOnChange' => true,
            'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
        )
            ));
    ?>
<p class="info"><?php echo t('site', 'Compila i campi per creare il tuo conto azienda (I campi con <span class="red">*</span> sono obbligatori):') ?></p>
    
    <div class="box-form">
        <h3 class="f-headline"><?php echo t('site', 'Dati conto') ?></h3>
        <div class="row clearfix">
            <label for="username" class="required"><?php echo t('site', 'Nome utente:'); ?></label>
            <?php echo $form->textField($model, 'username', array()); ?>
            <a class="tooltip" title="<?php echo t('site', 'Tra 4 e 20 caratteri (AZ, az, 0-9), senza spazi. 
    Senza punteggiatura. <br /> Atenzione qusta sara anche norme del url del shop. Ex: <strong>nomeutente.arrafriclub.it</strong>'); ?>"></a>
               <?php echo $form->error($model, 'username'); ?>
        </div>
        <div class="row clearfix">
            <label for="email" class="required"><?php echo t('site', 'E-mail:'); ?></label>
            <?php echo $form->textField($model, 'email', array()); ?>
            <a class="tooltip" title="<?php echo t('site', 'Inserisci il tuo indirizzo e-mail con attenzione. Riceverài una mail di conferma.'); ?>"></a>
            <?php echo $form->error($model, 'email'); ?>
        </div>
        <div class="row clearfix">
            <label for="password" class="required"><?php echo t('site', 'Password:'); ?></label>
            <?php echo $form->passwordField($model, 'password', array()); ?>
            <a class="tooltip" title="<?php echo t('site', 'Usa da 6 a 15 caratteri. Almeno un numero e una lettera. Non inserire una <br />sequenza di un stesso carattere.'); ?>"></a>
            <?php echo $form->error($model, 'password'); ?>
        </div>
        <div class="row clearfix">
            <label for="verifyPassword" class="required"><?php echo t('site', 'Password Confirm'); ?></label>
            <?php echo $form->passwordField($model, 'verifyPassword', array('autoComplete' => 'off')); ?>
            <?php echo $form->error($model, 'verifyPassword'); ?>
        </div>
        <div class="row clearfix">
            <label for="firstname" class="required"><?php echo t('site', 'First name:'); ?></label>
            <?php echo $form->textField($model, 'firstname', array('autoComplete' => 'off')); ?>
            <?php echo $form->error($model, 'firstname'); ?> 
        </div>
        <div class="row clearfix">
            <label for="lastname" class="required"><?php echo t('site', 'Last name:'); ?></label>
            <?php echo $form->textField($model, 'lastname', array('autoComplete' => 'off')); ?>
            <?php echo $form->error($model, 'lastname'); ?>
        </div>
    </div>
    <br />
    <div class="box-form">
        <h3 class="f-headline"><?php echo t('site', 'Dati azienda') ?></h3>
        <div class="row clearfix">
            <label for="companyname" class="required"><?php echo t('site', 'Nome azienda:'); ?></label>
            <?php echo $form->textField($model, 'companyname', array('autoComplete' => 'off')); ?>
            <?php echo $form->error($model, 'companyname'); ?>
        </div>
        <div class="row clearfix">
            <label for="companytype" class="required"><?php echo t('site', 'Tipo azienda:'); ?></label>
            <?php
            echo $form->dropDownList($model, 'companytype', array(
                '1' => t('site', 'Manufacturer'),
                '2' => t('site', 'Distributor'),
                '3' => t('site', 'Wholesaling'),
                '4' => t('site', 'Retailer'),
                '5' => t('site', 'Service provider'),
                '6' => t('site', 'Intermediate'),
                '7' => t('site', 'Importer')), array('empty' => t('site', '--Select type--')));
            ?>
            <?php echo $form->error($model, 'companytype'); ?>
        </div>
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
                <label for="province_id" class="required"><?php echo t('site', 'Provincia'); ?></label>	 
                <?php echo $form->dropDownList($model, 'province_id', Province::getProvinceFromRegion($model->region_id, false), array('empty' => t('site', '--Select province--'), 'options' => array($model->province_id => array('selected' => true)))); ?>
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
        <div class="row clearfix">
            <label for="phone" class="required"><?php echo t('site', 'Telefono:'); ?></label>
            <?php echo $form->textField($model, 'phone'); ?>
            <?php echo $form->error($model, 'phone'); ?>
        </div>
        <div class="row clearfix">
            <label for="vat_code" class="required"><?php echo t('site', 'Partida IVA:'); ?></label>
            <?php echo $form->textField($model, 'vat_code'); ?>
            <?php echo $form->error($model, 'vat_code'); ?>
        </div>
        <div class="row clearfix">
            <label for="bank_name" class="required"><?php echo t('site', 'Nome della banca:'); ?></label>
            <?php echo $form->textField($model, 'bank_name'); ?>
            <?php echo $form->error($model, 'bank_name'); ?>
        </div>
        <div class="row clearfix">
            <label for="bank_iban" class="required"><?php echo t('site', 'Codice IBAN:'); ?></label>
            <?php echo $form->textField($model, 'bank_iban'); ?>
            <?php echo $form->error($model, 'bank_iban'); ?>
        </div>
    </div>

    <div class="box-form-a">
        <div class="row clearfix captcha">
            <?php
            $this->widget('CCaptcha', array(
                'buttonLabel' => t('site', 'Genera nuovo codice'),
                'buttonOptions' => array('class' => 'refreshCode')
            ));
            ?>
        </div>
        <div class="row clearfix captcha">
            <label for="verifyCode" class="required"><?php echo t('site', 'Codice di sigurezza:'); ?></label>
            <?php echo $form->textField($model, 'verifyCode', array('class' => 'sCode')); ?>
            <a class="tooltip" title="<?php echo t('site', 'Inserisci il codice della figura.'); ?>"></a>
            <?php echo $form->error($model, 'verifyCode'); ?>
        </div>
        <div class="row clearfix first">
            <label for="email_news"><?php echo t('site', 'Newsletter'); ?></label>
            <?php echo $form->checkBox($model, 'email_news', array('checked' => 'checked')); ?>
            <?php //echo $form->error($model, 'email_news');  ?>
        </div>
        <div class="row clearfix">
            <label for="terms">
                <?php echo t('site', 'I accept the ') . CHtml::link(t('site', 'terms and conditions'), '#'); ?>
            </label>
            <?php echo $form->checkBox($model, 'terms', array('checked' => 'checked')); ?>
            <?php echo $form->error($model, 'terms'); ?>
        </div>
        <div class="actions">

            <?php echo CHtml::submitButton(t('site', 'Continua'), array('class' => 'btn-i-n i-green', 'name' => 'next')); ?>

        </div>
    </div>
    <?php $this->endWidget(); ?> 
</div>
