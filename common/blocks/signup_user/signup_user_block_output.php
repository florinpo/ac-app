<div class="box_round_c grid_24">
    <h1 class="reg-headline"><?php echo t('site', 'Registrazione utente a AffariClub.it') ?></h1>
    <div class="reg-step">
        <span class="selected"><?php echo t("site", "1. Creare conto"); ?></span>
        <span><?php echo t("site", "2. Verifica email"); ?></span>
        <span><?php echo t("site", "3. Registrazione finita"); ?></span>
    </div>
    <p class="info"><?php echo t('site', 'Compila i campi per creare il tuo conto membro (I campi con <span class="red">*</span> sono obbligatori):') ?></p>
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
        'id' => 'user-register-form',
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
        ),
            ));
    ?>
    <div class="row clearfix">
        <label for="username" class="required"><?php echo t('site', 'Nome utente:'); ?></label>
        <?php echo $form->textField($model, 'username', array()); ?>
        <a class="tooltip" title="<?php echo t('site', 'Tra 4 e 20 caratteri (AZ, az, 0-9), senza spazi. Senza punteggiatura.'); ?>"></a>
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
        </div>
        <div class="row clearfix">
            <label for="terms">
                <?php echo t('site', 'I accept the ') . CHtml::link(t('site', 'terms and conditions'), '#'); ?>
            </label>
            <?php echo $form->checkBox($model, 'terms', array('checked' => 'checked')); ?>
            <?php echo $form->error($model, 'terms'); ?>
        </div>

        <div class="actions">
            <?php echo CHtml::submitButton(t('site', 'Continua'), array('class' => 'btn-i-n i-green')); ?>
        </div>

        <?php $this->endWidget(); ?> 
    </div>
</div>


