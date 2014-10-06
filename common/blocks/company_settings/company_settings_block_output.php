
<section id="product-add-edit" class="box_widget grid_18 omega">
    <h1 class="b-dotted"><?php echo t('site', 'Impostazioni conto'); ?></h1>
    <div class="form-wrapper">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'company-settings-form',
            'htmlOptions' => array('autocomplete' => 'off', 'class'=>'settings-form'),
            'enableAjaxValidation' => false,
            'enableClientValidation' => false
            ));
        ?>
        <div class="box-form">
        <div class="row first">
            <?php echo $form->checkBox($model, 'email_news'); ?>
            <label for="email_news">
             <?php echo t('site', 'I agree to receive {sitename}\'s Newsletter', array(
                '{sitename}' => settings()->get('general', 'site_name')));
             ?>
            </label>
            <?php echo $form->error($model, 'email_news'); ?>
        </div>
        <div class="row">
            <?php echo $form->checkBox($model, 'email_message'); ?>
            <label for="email_message" style="display: inline; ">
               <?php echo t('site', 'I want to be notified  by email when new message arrives'); ?>
            </label>
            <?php echo $form->error($model, 'email_message'); ?>
        </div>

        <div class="row">
            <?php echo $form->checkBox($model, 'email_status'); ?>
            <label for="email_status" style="display: inline; ">
               <?php echo t('site', 'I want to be notified by email  when my account status has changed'); ?>
            </label>
            <?php echo $form->error($model, 'email_status'); ?>
        </div>
        <div class="row">
            <?php echo $form->checkBox($model, 'email_traffic'); ?>
            <label for="email_traffic">
               <?php echo t('site', 'I agree to receive by email traffic report of my company pages'); ?>
            </label>
            <?php echo $form->error($model, 'email_traffic'); ?>
        </div>
        <div class="row">
            <?php echo $form->checkBox($model, 'email_inquiry'); ?>
            <label for="email_inquiry" style="display: inline; ">
                <?php echo t('site', 'I want to be notified by email when new inquiry arrives'); ?>
            </label>
        <?php echo $form->error($model, 'email_inquiry'); ?>
        </div>
        </div>
        <div class="box-form-a">
            <div class="actions">
                <?php echo CHtml::submitButton(t('site', 'Salva'), array('class' => 'btn-i-n i-green', 'id' => 'saveBtn')); ?>
            </div>
        </div> 
    <?php $this->endWidget(); ?>
    </div>
</section>





