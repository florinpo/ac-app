<!-- begin cat-widget -->
<div id="categories_main" class="box_widget grid_5 alpha">
    <div class="widget_header">
        <h2>Categories</h2>
    </div>
    <ul id="cat-listing-home">
        <?php foreach($categories as $category): ?>
        <li>
            <?php echo CHtml::link(CHtml::encode($category->name),array('page/render', 'slug'=>'prodotti', 'cat'=>$category->slug."-".$category->id)); ?>
         </li>
        <?php endforeach; ?>
    </ul>
</div>
<!-- end cat-widget -->