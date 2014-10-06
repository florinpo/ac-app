
<li class="clearfix">
    <input class="view-contact" id="c_<?php echo $data->contact_id; ?>" type="checkbox" name="contacts[]" value="<?php echo $data->contact_id; ?>" />

<spam class="cUser">
    <?php echo GxcHelpers::getDisplayName($data->contact_id, true, true); ?>
</spam>
</li>
