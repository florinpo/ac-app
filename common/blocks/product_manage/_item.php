<?php
$rowCssClass = array('odd', 'even');

$classes = array();

if ($index % 2 == 0) {
    $classes [] = 'first';
} else {
    $classes [] = 'last';
}

if (is_array($rowCssClass) && !empty($rowCssClass)) {
    $classes [] = $rowCssClass[$index % count($rowCssClass)];
}
$classes = implode(' ', $classes);
?>

<tr class="<?php echo $classes; ?>">
<td class="checkbox-column">
    <input type="checkbox" name="Id[]" id="Id_<?php echo $index; ?>" value="<?php echo $data->id; ?>" class="select-on-check">
</td>
<td class="grid_left">
    <div class="product-item-wrapper clearfix">
        <div class="item-thumbnail"><?php echo $data->selectedImage(120); ?> </div>
        <div class="item-info">
            <div class="item-title">
                <?php echo CHtml::link($data->name, app()->createUrl("page/render", array("slug" => "product-view", "id" => $data->id))) ?>
            </div>
            <div class="item-actions clearfix">
                <div class="grid_2 alpha">
                    <?php
                    echo CHtml::link("<span class='icon-pencil iconb'></span>", app()->createUrl("page/render", array("slug" => "product-form", "id" => $data->id)), array("class" => "bDefault btnCtrlM bFirst t-middle", "id" => "edit", "title"=>'Edit this offer'));
                    echo CHtml::link("<span class='icon-eye-open iconb'></span>", "#", array("class" => "bDefault btnCtrlM t-middle", "id" => "view", "title"=>'View on website'))
                    ?>
                </div>
                <div class="grid_8 omega">
                    <span class="vd-separator"></span>
                    <div class="floatL ilabel"><?php echo t("site", "Visible in main page?"); ?></div>
                    <div class="yes_no floatL">
                        <?php
                        echo $data->getVbuttons($data->id, $data->visible_home);
                        echo CHtml::hiddenField("pshop_" . $data->id, $data->shop->id);
                        ?>
                    </div>
                </div>
            </div>
            <div class="item-date clearfix">
                <span><?php echo t("site", "Aggiornato nell") . " - " . simple_date($data->update_time) ?></span>
            </div>
        </div>
    </div>
</td>
<td class="grid_100 alignC status">
    <?php echo $data->getStatusString(); ?>
</td>
</tr>




