<!-- begin search-bar -->
<div id="" class="shop-search-wrapper clearfix">
    <?php echo CHtml::beginForm('', 'post', array('id' => 'shop-search-form')); ?>
    <?php echo CHtml::textField($search, 'keyword', array('id' => 'keyword')); ?>
    <?php echo CHtml::SubmitButton(t('site', 'Search'), array('id' => 'search-submit')); ?>
    <?php echo CHtml::endForm(); ?>
</div>
<!-- end search-bar -->