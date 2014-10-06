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
    <input type="checkbox" name="Id[]" id="Id_<?php echo $index; ?>" value="<?php echo $data['id']; ?>" class="select-on-check">
</td>
<td class="grid_left">
    <?php echo $data['title']; ?>
</td>
<td class="grid_100 alignC">
    <?php echo $data['visits']; ?>
</td>
<td class="grid_100 alignC">
    <?php //echo $data['visits']; ?>
</td>
</tr>




