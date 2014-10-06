<div class="confirmation">
    <div><?php
        echo t('site', 'Your account has been successfully created. In order to login you need to authenticate your email address. We have sent a according message with instructions to your email address. If you don\'t find the email, check your spam-box.');
        ?>
    </div>
   <a href="<?php echo bu();?>/resend_activation"><?php echo Yii::t('FrontendMessage',' Resend Activation');?></a>
</div>