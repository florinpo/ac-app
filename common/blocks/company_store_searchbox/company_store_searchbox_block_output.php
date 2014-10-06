<!-- begin search-bar -->
<div class="search-wrap-wide">
    <?php echo CHtml::beginForm('', 'post', array('id' => 'search-form')); ?>
    <div class="grid_2">
         <label for="keyword"><?php echo t('site', 'Cerca:'); ?></label>
    </div>
    <div class="grid_22">
        <?php
        echo CHtml::activeDropDownList($search, 'type', $this->getContentType(false, $company->cprofile->companyname), array(
            'options' => array($this->selectedCtype() => array('selected' => true)),
            'id' => 'search-options'
        ));
        ?>
        <?php echo CHtml::activeTextField($search, 'keyword', array('id' => 'keyword')); ?>
        <?php echo CHtml::SubmitButton(t('site', 'Search'), array('id' => 'search-submit')); ?>
    </div>
    <?php echo CHtml::endForm(); ?>
</div>
<!-- end search-bar -->