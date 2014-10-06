<!-- start products_categories -->
<section id="companies_categories" class="grid_18 alpha box_widget">
    <div class="header">
        <h2><?php echo t('site', 'Categorie aziende') ?></h2>
    </div>
    <div class="content">
        <?php if (count($categories) > 0): ?>
        <?php foreach (array_chunk($categories, 3) as $row): ?>
            <ul class="catalog-categories clearfix">
                <?php
                foreach ($row as $n => $category) {
                    $subcategories = $category->descendants()->findAll();
                    $subcats_lev2 = $category->children()->findAll();

                    $subcategories_ids = array();
                    foreach ($subcategories as $subcat) {
                        $subcategories_ids[] = $subcat->id;
                    }
                    $subcats_lev2_id = array();
                    $subcats_exclude = array();
                    echo "<li>";
                    echo CHtml::link(CHtml::encode($category->name), array('page/render', 'slug' => $slug, 'cat' => $category->slug . "-" . $category->id));
                    if (count($subcats_lev2) > 0) {
                        foreach ($subcats_lev2 as $subcat) {
                            $subcats_lev3 = $subcat->children()->findAll();
                            if (count($subcats_lev3) > 0) {
                                $subcats_lev2_ids[] = $subcat->id;
                                $subcats_exclude = array_diff($subcategories_ids, $subcats_lev2_ids);
                                $subcats_exclude = array_values($subcats_exclude); // we reorder the array after the exclude
                            } else {
                                $subcats_exclude[] = $subcat->id;
                            }
                        }
                        echo '<ul id="cat_' . "$category->id" . '">';


                        foreach ($subcats_exclude as $k => $id) {
                            if ($k < 4) {
                                echo '<li id="cat_' . "$category->id" . '_' . "$k" . '">';
                                $subcategory = CompanyCats::model()->findByPk($subcats_exclude[$k]);
                                echo CHtml::link(CHtml::encode($subcategory->name), array('page/render', 'slug' => $slugview, 'cat' => $category->slug . "-" . $category->id, 'subcat' => $subcategory->slug . "-" . $subcategory->id));
                                echo '</li>';
                                $k++;
                            } else if ($k >= 4) {
                                $k = $k - 1;
                                echo '<li style="display:none;" id="cat_' . "$category->id" . '_' . "$k" . '">';
                                $subcategory = CompanyCats::model()->findByPk($subcats_exclude[$k]);
                                echo CHtml::link(CHtml::encode($subcategory->name), array('page/render', 'slug' => $slugview, 'cat' => $category->slug . "-" . $category->id, 'subcat' => $subcategory->slug . "-" . $subcategory->id));
                                echo '</li>';
                                $k++;
                            }
                        }
                        if (count($subcats_exclude) > 4) {
                            echo '<li>';
                            echo CHtml::link(t('site', 'Mostra tutte'), 'javascript:void(0)', array('class'=>'expand', 'id' => 'all_' . $category->id, 'onclick' => '$.fn.expandCatCategs(' . $category->id . ',' . count($subcats_exclude) . ',4); return false;'));
                            echo '</li>';
                        }
                        echo '</ul>';
                    }

                    echo "</li>";
                }
                ?>
            </ul>
        <?php endforeach; ?>

        <?php else: ?>
            <p><?php echo t('site', 'No results found for categories'); ?></p>
        <?php endif; ?>
        <div class="clear"></div>
    </div>
</section>
<!-- end companies_categories -->


