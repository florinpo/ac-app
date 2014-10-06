<section id="change-password" class="box_widget grid_18 omega">
    <h1 class="b-dotted"><?php echo t('site', 'Cambia password'); ?></h1>
    <p class="info">
        <?php echo t('site', 'Cambia la tua password corrente con una nuova 
            (I campi con <span class="red">*</span> sono obbligatori):') ?>
    </p>
    <div class="form-wrapper">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'changepass-form',
            'htmlOptions' => array('autocomplete' => 'off', 'class' => 'change-password-form'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
                )));
        ?>
        <div class="box-form">
            <div class="row clearfix first">
                <label for="old_password" class="required"><?php echo t('site', 'Vecchia password:'); ?></label>
                <?php echo $form->passwordField($model, 'old_password', array()); ?>              
                <?php echo $form->error($model, 'old_password', array()); ?>
            </div>
            <div class="row clearfix">
                <label for="new_password_1" class="required"><?php echo t('site', 'Nuova password:'); ?></label>
                <?php echo $form->passwordField($model, 'new_password_1', array()); ?>
                <a class="tooltip" rel="tipsy" href='javascript:void(0);' onclick='$("#UserChangePassForm_new_password_1").tipsy("show"); return false;'></a>
                <?php echo $form->error($model, 'new_password_1', array()); ?>
            </div>
            <div class="row clearfix">
                <label for="new_password_2" class="required"><?php echo t('site', 'Conferma nuova password:'); ?></label>
                <?php echo $form->passwordField($model, 'new_password_2', array()); ?>              
                <?php echo $form->error($model, 'new_password_2', array()); ?>
            </div>
        </div>
        <div class="actions">
            <?php echo CHtml::submitButton(t('site', 'Cambia'), array('class' => 'btn-i-n i-green', 'id' => 'saveBtn')); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>





