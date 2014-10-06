
<div class="box_widget grid_24 alpha">
    <h1 class="reg-headline"><?php echo t("site", "Registrazione utente a AffariClub.it") ?></h1>
    <div class="reg-step">
        <span><?php echo t("site", "1. Creare conto");?></span>
        <span><?php echo t("site", "2. Verifica email");?></span>
        <span class="selected"><?php echo t("site", "3. Registrazione finita");?></span>
    </div>
    <div class="confirmation">
    <p>
        <?php
        echo t("site", "Il tuo account è stato attivato con successo.")
        ?>
    </p>
    <p>
       <?php echo CHtml::link(t("site","Clicca qui"), array("page/render", "slug"=>"dashboard")).t("site", "se il tuo browser non ti reindirizza automaticamente."); ?>
    </p>
    
    <div>
</div>









