<div class="box_widget grid_16 alpha">
    aklsalskla
</div>
<div id="login-panel" class="box_widget grid_8 omega">
    <h2><?php echo t('site', 'Entra nel conto'); ?></h2>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
            )));
    ?>    
    <div class="row">
        <label for="username" class="labelBlur"><?php echo t('site', 'E-mail o nome utente:'); ?></label>
        <?php echo $form->textField($model, 'username', array('autoComplete' => 'off')); ?>
        <?php echo $form->error($model, 'username'); ?>
    </div>             
    <div class="row">
        <label for="password" class="labelBlur"><?php echo t('site', 'Password:'); ?></label>
        <?php echo $form->passwordField($model, 'password', array('autoComplete' => 'off')); ?>
        <?php echo $form->error($model, 'password'); ?>
    </div>

    <div class="row">
        <label for="rememberMe" class="remember-me"><?php echo t('site', 'Tienimi connesso 24 ore') ?></label>
        <?php echo $form->checkBox($model, 'rememberMe', array()); ?>
        <?php echo $form->error($model, 'rememberMe'); ?>
        <p><?php
        echo t('site', 'Ho dimenticato il nome_utente o la password', array(
            'nome_utente' => CHtml::link(t('site', 'nome utente'), '#'),
            'password' => CHtml::link(t('site', 'password'), '#')
        ));
        ?></p>
    </div>
    <div class="actions">
        <span class="bGreenN-wrap"><?php echo CHtml::submitButton(t('site', 'Accedi'), array('class' => 'btn-i-n i-green')); ?></span>
    </div>
    <?php $this->endWidget(); ?>
    <div class="login-info">
        <p><?php echo t('site', 'Non ancora membro AffariClub?');?></p>
        <?php echo CHtml::link('<span class="inner"><span class="text">' . t('site', 'Registrati ora!') . '</span></span>', array('page/render', 'slug' => 'register'), array('class' => 'btn-s black')) ?>
    </div>
</div>

