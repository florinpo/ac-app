
<div class="form-stacked" >
    <?php $this->render('cmswidgets.views.notification_frontend'); ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'company-settings-form',
        'htmlOptions' => array('autocomplete' => 'off'),
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
        ),
       ));
    ?>
   <div class="row">
        <?php echo $form->checkBox($model, 'email_news'); ?>
        <label for="email_news" style="display: inline; "><?php echo Yii::t('FrontendUser','I agree to receive {sitename}\'s Newsletter', array(
            '{sitename}' => settings()->get('general', 'site_name'))); ?>
        </label>
       
        <?php echo $form->error($model, 'email_news'); ?>
    </div>
    
    <div class="row">
         <?php echo $form->checkBox($model, 'email_message'); ?>
        <label for="email_message" style="display: inline; ">
            <?php echo Yii::t('FrontendUser','I want to be notified  by email when new message arrives'); ?>
        </label>
        <?php echo $form->error($model, 'email_message'); ?>
    </div>
    
    <div class="row">
         <?php echo $form->checkBox($model, 'email_status'); ?>
        <label for="email_status" style="display: inline; ">
            <?php echo Yii::t('FrontendUser','I want to be notified by email  when my account status has changed'); ?>
        </label>
        <?php echo $form->error($model, 'email_status'); ?>
    </div>
    
    <div class="row">
         <?php echo $form->checkBox($model, 'email_traffic'); ?>
        <label for="email_traffic" style="display: inline; ">
            <?php echo Yii::t('FrontendUser','I agree to receive by email traffic report of my company pages'); ?>
        </label>
        <?php echo $form->error($model, 'email_traffic'); ?>
    </div>
    
    <div class="row">
         <?php echo $form->checkBox($model, 'email_inquiry'); ?>
        <label for="email_inquiry" style="display: inline; ">
            <?php echo Yii::t('FrontendUser','I want to be notified by email when new inquiry arrives'); ?>
        </label>
        <?php echo $form->error($model, 'email_inquiry'); ?>
    </div>

    <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('FrontendUser', 'Update')); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>





