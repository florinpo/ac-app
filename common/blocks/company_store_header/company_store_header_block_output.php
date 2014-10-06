<?php
$layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.store.assets'));
?>
<div class="grid_5 omega">
    <?php if($company->has_membership==1): ?>
    <img src="<?php echo $layout_asset; ?>/images/logos/premium1.png" alt="<?php echo $company->cprofile->companyname.' '.t('site',' membro premium'); ?>"/>
    <?php else: ?>
    <img src="<?php echo $layout_asset; ?>/images/logos/free1.png" alt="<?php echo $company->cprofile->companyname.' '.t('site',' membro gratuito'); ?>"/>
    <?php endif; ?>
</div>

<div class="grid_11 omega">
    <h1><?php echo $company->cprofile->companyname; ?></h1>
</div>