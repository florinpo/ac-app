<div name="div-block-content-<?php echo $block_model->id; ?>">
    <div class="row">
        <?php echo CHtml::label(Block::getLabel($block_model, 'display_type'), ''); ?>
        <?php echo CHtml::dropDownList("Block[display_type]", $block_model->display_type, ContactListBlock::getDisplayTypes(), array('id' => 'Block-display_type'));
        ?>
        <?php echo $form->error($model, 'display_type'); ?>
    </div> 
</div>