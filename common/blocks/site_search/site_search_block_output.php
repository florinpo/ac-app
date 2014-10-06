<!-- begin search-bar -->
<div id="search-bar-wrapper" class="search-wrap grid_19">
    <?php echo CHtml::beginForm('', 'post', array('id' => 'search-form')); ?>
    <label for="keyword"><?php echo t('site', 'Cerca:'); ?></label>
    <?php
    echo CHtml::activeDropDownList($search, 'type', GxcHelpers::getContentType(false), array(
        'options' => array($this->selectedCtype() => array('selected' => true)),
        'id' => 'search-options'
    ));
    ?>
    <?php echo CHtml::activeTextField($search, 'keyword', array('id' => 'keyword')); ?>
    <?php echo CHtml::SubmitButton(t('site', 'Search'), array('id' => 'search-submit')); ?>
    <?php echo CHtml::endForm(); ?>
</div>
<!-- end search-bar -->



