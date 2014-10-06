<!-- start companies_subcategories -->
<section id="companies_subcategories" class="grid_18 alpha box_widget">
    <div class="header">
        <h2><?php echo $category->name; ?></h2>
    </div>
    <div class="content">
        <?php
        $subcategories = $category->children()->findAll();
        if (count($subcategories) > 0):
            ?>
            <ul class="subcat-list">
                <?php
                foreach ($subcategories as $subcategory) {
                    $subcategories_lev2 = $subcategory->children()->findAll();
                    echo "<li>";
                    if (count($subcategories_lev2) > 0) {

                        echo CHtml::link(CHtml::encode($subcategory->name), 'javascript:void(0);', array('style' => 'font-weight:bold'));

                        echo "<ul>";
                        foreach ($subcategories_lev2 as $subcategory_lev2) {
                            echo "<li>";
                            echo CHtml::link(CHtml::encode($subcategory_lev2->name), array('page/render', 'slug' => $slug, 'cat' => $category->slug . "-" . $category->id, 'subcat' => $subcategory_lev2->slug . "-" . $subcategory_lev2->id));

                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo CHtml::link(CHtml::encode($subcategory->name), array('page/render', 'slug' => $slug, 'cat' => $category->slug . "-" . $category->id, 'subcat' => $subcategory->slug . "-" . $subcategory->id));
                    }
                    echo "</li>";
                }
                ?>
            </ul>
        <?php else: ?>
            <p><?php echo t('site', 'No results found for this category'); ?></p>
        <?php endif; ?>
            <div class="clear"></div>
    </div>
</section>
<!-- end companies_subcategories -->
