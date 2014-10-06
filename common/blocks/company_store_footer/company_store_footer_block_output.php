<div id="footer-top" class="grid_16">
    <div class="grid_5 omega">
        <h3><?php echo $company->cprofile->companyname; ?></h3>
    </div>
    <div class="grid_11 omega">
        <ul id="f-navigation" class="clearfix">
            <li>
                <?php
                echo CHtml::link(t('site', 'Home'), array('site/store', 'username' => $company->username));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Prodotti'), array('site/store', 'username' => $company->username, 'page_slug' => 'elenco-vendita'));
                ?>
            </li>
            <li>
                <?php
                echo CHtml::link(t('site', 'Contatti'), array('site/store', 'username' => $company->username, 'page_slug' => 'contatto'));
                ?>
            </li>
        </ul>
        <div class="info">
            <?php if($company->has_membership==1): ?>
            <p><?php echo t('site', 'Aceasta companie este o companie MembroPremium.'); ?></p>
            <p><?php echo t('site', 'Pentru mai multe informatii legate de avantajele extraordinare pe care le are un MaxMember'); ?></p>
            <?php else: ?>
            <p><?php echo t('site', 'Informatiile acestei companii nu au fost autentificate si verificate de catre AffariClub.it'); ?></p>
            <p><?php echo t('site', 'Pentru mai multe informatii legate de avantajele extraordinare pe care le are un MaxMember'); ?></p>
            <?php endif;?>
        </div>
    </div>

</div>