<section id="member-profile" class="box_round_c grid_19 omega">
    <h1 class="dash"><?php echo t('site', 'Dati conto'); ?></h1>
    
    <div class="form-wrapper">
        <p class="info"><?php echo t('site', 'Modifica profilo utente. Sulla base sottostante verrai contattato da potenziali clienti (I campi con <span class="red">*</span> sono obbligatori):') ?></p>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'member-profile-form',
            'htmlOptions' => array('autocomplete' => 'off', 'class' => 'profile-form'),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validationUrl' => bu() . '/ajax', //Point to any ajax page you want
            )
                ));
        ?>

        <div class="box-form">
            <h3 class="f-headline"><?php echo t('site', 'Dati personali') ?></h3>
            <div class="row clearfix">
                <label for="firstname" class="required"><?php echo t('site', 'Nome:'); ?></label>
                <?php echo $form->textField($model, 'firstname', array()); ?>
                <?php echo $form->error($model, 'firstname'); ?> 
            </div>
            <div class="row clearfix">
                <label for="lastname" class="required"><?php echo t('site', 'Cognome:'); ?></label>
                <?php echo $form->textField($model, 'lastname', array()); ?>
                <?php echo $form->error($model, 'lastname'); ?>
            </div>

            <div class="row clearfix">
                <label for="companyposition"><?php echo t('site', "Ruolo nell'azienda:"); ?></label>
                <?php
                echo $form->dropDownList($model, 'companyposition', array('1' => t('site', 'Director'),
                    '2' => t('site', 'General Manager'),
                    '3' => t('site', 'Company Owner'),
                    '4' => t('site', 'Sales'),
                    '5' => t('site', 'Marketing'),
                    '6' => t('site', 'Administration'),
                    '7' => t('site', 'Other')), array('empty' => ''));
                ?>
                <?php echo $form->error($model, 'companyposition'); ?>
            </div>
        </div>
        <div class="box-form">
            <h3 class="f-headline"><?php echo t('site', 'Dati impressa:') ?></h3>
            <div class="row clearfix">
                <label for="company" class="required"><?php echo t('site', 'Nome azienda:'); ?></label>
                <?php echo $form->textField($model, 'companyname', array('class' => 'userform', 'autoComplete' => 'off')); ?>
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
                    '7' => t('site', 'Importer')), array('empty' => t('site', '')));
                ?>
                <?php echo $form->error($model, 'companytype'); ?>
            </div>
            <div class="row clearfix">
                <label for="domain_id" class="required"><?php echo t('site', 'Macrosettore:'); ?></label>
                <?php
                echo $form->dropDownList($model, 'domain_id', CompanyCats::getDomains(false), array(
                    'options' => array($model->domain_id => array('selected' => true)),
                    'empty' => t('site', '')
                ));
                ?>
                <?php echo $form->error($model, 'domain_id'); ?>
            </div>


            <div class="row clearfix">
                <label for="vat_code" class="required"><?php echo t('site', 'Partida IVA:'); ?></label>
                <?php echo $form->textField($model, 'vat_code'); ?>
                <?php echo $form->error($model, 'vat_code'); ?>
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
        </div>

        <div class="box-form">
            <h3 class="f-headline"><?php echo t('site', 'Recapiti:') ?></h3>
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
            <div class="row clearfix">
                <label for="website"><?php echo t('site', 'Sito web:'); ?></label>
                <?php echo $form->textField($model, 'website', array('title' => 'Inserisca il sito web. Ex: www.miosito.it')); ?>
                <?php echo $form->error($model, 'website'); ?>
            </div>
        </div>
        <div class="box-form-a center">
            <div class="actions">
                <?php echo CHtml::submitButton(t('site', 'Salva'), array('class' => 'btn-i-n i-green')); ?>
            </div>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</section>