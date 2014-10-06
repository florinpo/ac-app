<?php 
$this->pageTitle=t('cms','Manage Payments');
$this->pageHint=t('cms','Here you can manage payments');
?>
<?php
$this->widget('cmswidgets.billing.BillingManageWidget',array());
?>