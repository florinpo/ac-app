<!-- begin breadcrumbs -->
<div id="breadcrumbs" class="grid_24 alpha">
    <div class="inner">
        <ul>
        <?php
        $this->widget('cms.components.BreadCrumbs', array(
            'links' => $this->breadcrumbs,
            'separator' => '',
        ));
        ?>
        </ul>
    </div>
</div>
<!-- end breadcrumbs -->