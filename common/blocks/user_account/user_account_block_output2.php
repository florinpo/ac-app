<section id="user-profile" class="box_round_c grid_19 omega">
    <h2 class="dash"><?php echo t('site', 'Dati conto'); ?></h2>

    <div class="form-wrapper">
        <p class="info"><?php echo t('site', 'Modifica profilo utente. Sulla base sottostante verrai contattato da potenziali clienti (I campi con <span class="red">*</span> sono obbligatori):') ?></p>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'user-profile-form',
            'htmlOptions' => array('autocomplete' => 'off', 'class' => 'profile-form'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
                'validateOnSubmit' => true,
                'validateOnChange' => false,
            )
                ));
        ?>
        <div class="row">
            <label for="firstname"><?php echo t('site', 'First name'); ?></label>
            <?php echo $form->textField($model, 'firstname', array('size' => 30, 'class' => 'userform', 'autoComplete' => 'off')); ?>
            <?php echo $form->error($model, 'firstname'); ?> 
        </div>

        <div class="row">
            <label for="lastname"><?php echo t('site', 'Last name'); ?></label>
            <?php echo $form->textField($model, 'lastname', array('size' => 30, 'class' => 'userform', 'autoComplete' => 'off')); ?>
            <?php echo $form->error($model, 'lastname'); ?>
        </div>


        <div class="row">
            <label for="gender"><?php echo t('site', 'Gender'); ?></label>
            <?php echo $form->radioButtonList($model, 'gender', array('male' => t('site', 'Male'), 'female' => t('site', 'Female')), array('separator' => '', 'class' => 'radio_btn')); ?>
            <span class="clear"></span>
            <?php echo $form->error($model, 'gender'); ?>
        </div>


        <div class="row">
            <label for="birthday"><?php echo t('site', 'Date of birth'); ?></label>
            <?php
            $this->widget('CMaskedTextField', array(
                'model' => $model,
                'attribute' => 'birthday',
                'mask' => '99/99/9999',
                'htmlOptions' => array('size' => 20, 'placeholder' => 'dd/mm/aaaa')
            ));
            ?>
            <?php echo $form->error($model, 'birthday'); ?>     
        </div>

        <div class="row">
            <label for="region_id"><?php echo t('site', 'Region'); ?></label>    
            <?php
            echo $form->dropDownList($model, 'region_id', Province::getRegion(), array(
                'ajax' => array(
                    'type' => 'POST', //request type
                    'url' => app()->createUrl('site/provinceFromRegion'),
                    'data' => array('region_id' => 'js:this.value', 'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()),
                    'update' => '#' . CHtml::activeId($model, 'province_id'),
                    ))
            );
            ?>
            <?php echo $form->error($model, 'region_id'); ?>
        </div>

        <?php $get_province_id = isset($_GET['province_id']) ? (int) ($_GET['province_id']) : null ?>
        <?php if ($get_province_id === null) : ?>
            <div class="row">
                <label for="region_id"><?php echo t('site', 'Province'); ?></label>	 
                <?php echo $form->dropDownList($model, 'province_id', Province::getProvinceFromRegion($model->region_id, false), array('options' => array($model->province_id => array('selected' => true)))); ?>
                <?php echo $form->error($model, 'province_id'); ?>
            </div>
        <?php else : ?>
            <?php echo $form->hiddenField($model, 'province_id', array('value' => $get_province_id)); ?>
        <?php endif; ?>

        <div class="row">
            <label for="location"><?php echo t('site', 'Location'); ?></label>
            <?php echo $form->textField($model, 'location'); ?>
            <?php echo $form->error($model, 'location'); ?>
        </div>

        <div class="row">
            <label for="adress"><?php echo t('site', 'Adress'); ?></label>
            <?php echo $form->textField($model, 'adress'); ?>
            <?php echo $form->error($model, 'adress'); ?>
        </div>
        <div class="row">
            <label for="phone"><?php echo t('site', 'Phone number'); ?></label>
            <?php echo $form->textField($model, 'phone'); ?>
            <?php echo $form->error($model, 'phone'); ?>
        </div>

        <div class="actions">
            <?php echo CHtml::submitButton(t('site', 'Save'), array('class' => 'btn primary')); ?>

        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>
