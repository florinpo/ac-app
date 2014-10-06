<div class="box_widget grid_24 alpha">
    <h1 class="reg-headline"><?php echo t("site", "Registrazione utente a AffariClub.it") ?></h1>
    <div class="reg-step">
        <span><?php echo t("site", "1. Creare conto");?></span>
        <span class="selected"><?php echo t("site", "2. Verifica email");?></span>
        <span><?php echo t("site", "3. Registrazione finita");?></span>
    </div>
    <div class="verification">
    <p>
        <?php
        echo t("site", "Il tuo account è stato attivato con successo. Abbiamo inviato un messaggio con le istruzioni al tuo indirizzo email. Se non trovi l'e-mail, controlla la tua casella di spam.")
        ?>
    </p>
    <p class="info">
        <?php
        echo t("site", "I conti che non saranno confermati in 30 giorni saranno automaticamente cancelati.")
        ?>
    </p>
    <p>
        <?php
        echo t("site", "<a href='#'>Clicca qui</a> se non hai ancora ricevutto l'email di conferma.");
        ?>
    </p>
    <?php $key=  User::model()->find(array('condition'=>'email=:email', 'params'=>array(':email'=>$email)))->user_activation_key; ?>
    <a href="<?php echo FRONT_SITE_URL.'register-user/?op=email-verification&key='.$key.'&email='.$email; ?>"><?php echo FRONT_SITE_URL.'register-user/op=email-verification&key='.$key.'&email='.$email;?></a>
   
    <div>
</div>









