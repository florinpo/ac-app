<div id="contact-b" class="nav-tabs-nd tabs">
    <ul class="nav clearfix">
        <li class="first"><?php echo CHtml::link(t('site', 'Contatti'), '#ctab-1', array()) ?></li>
        <li class="last"><?php echo CHtml::link(t('site', 'Dove siamo'), '#ctab-2', array()) ?></li>
    </ul>
    <div id="ctab-1" class="nav-body clearfix">
        <div class="row">
            <span class="label"><?php echo t('site', 'Nume companie:'); ?></span>
            <span class="data"><?php echo $company->cprofile->companyname; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Persoana contact:'); ?></span>
            <span class="data"><?php echo $company->full_name; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Indirizo:'); ?></span>
            <span class="data"><?php echo $company->cprofile->adress . ', ' . $company->cprofile->postal_code; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Provincia:'); ?></span>
            <span class="data"><?php echo $company->cprofile->province->name; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Localita:'); ?></span>
            <span class="data"><?php echo $company->cprofile->location; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Telefon:'); ?></span>
            <span class="data"><?php echo $company->cprofile->phone; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Fax:'); ?></span>
            <span class="data"><?php echo isset($company->cprofile->fax) ? $company->cprofile->fax : ''; ?></span>
        </div>
        <div class="row">
            <span class="label"><?php echo t('site', 'Mobile:'); ?></span>
            <span class="data"><?php echo isset($company->cprofile->mobile) ? $company->cprofile->mobile : ''; ?></span>
        </div>
        <div class="row last">
            <span class="label"><?php echo t('site', 'Website:'); ?></span>
            <span class="data"><?php echo isset($company->cprofile->website) ? $company->cprofile->website : ''; ?></span>
        </div>
    </div>

    <div id="ctab-2" class="nav-body clearfix">
        <p><?php echo t('site', 'Location map'); ?></p>
    </div>
</div>