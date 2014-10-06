
<!-- begin latest_products -->
<section id="latest_products" class="grid_12 omega slide_widget">
    <div class="widget_header">
        <h2><?php echo t('site', 'Latest products'); ?></h2>
    </div>

    <div class="carousel">
        <ul class="clearfix">
            <?php
            $limit = 5;
            $items = array();
            foreach ($products as $k => $product) {
                $items[] = array(
                    'id' => $product->id,
                    'name' => str_trim($product->name, 26),
                    'image' => $product->selectedImage(80)
                );
                
            }
            $selectedProducts = array_slice($items, 0, $limit);
            ?>

            <?php foreach ($selectedProducts as $k => $product): ?>
                <?php
                if ($limit == $k + 1) {
                    echo "<li id='".$product['id']."' class='last'>";
                } else {
                    echo "<li id='".$product['id']."'>";
                }
                ?>
                <div class="thumb">
                    <a href="#"><?php echo $product['image'] ?></a>
                </div>
                <div class="text-wrap">
                    <a href="#"><?php echo $product['name']; ?></a>
                </div>
                </li>
            <?php endforeach; ?>

        </ul>
        
        
        
        <!-- begin direction navigation -->

        <a id="latest-products-next" class="next" href="javascript:void(0);">&rsaquo;</a>
        
        
        
        <?php
        echo CHtml::ajaxLink('&lsaquo;', app()->createUrl('productsale/slideimages'), array(
            'type' => 'POST',
            'datatype' => 'json',
            'data' => array(
                'op'=>'prev',
                'firstl' => "js:$('#latest_products ul li:first').attr('id')",
                'lastl' =>  "js:$('#latest_products ul li:last').attr('id')",
                'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                ),
            'success' => "function(data) {
                $('#latest_products ul li').remove();
                var json = $.parseJSON(data);
                
                
                $(json).each(function(i,val){
                   $('#latest_products ul').append(
                   '<li id='+val.id+'><div class=\'thumb\'><a href=\'#\'>'+val.image+'</a></div>'
                   +'<div class=\'text-wrap\'><a href=\'#\'>'+val.name+'</a></div></li>');
                   
                });
               var list = $('#latest_products ul li'),
               total = list.length;
               list.each(function(i) {
                    if (i === total - 1) {
                       $(this).addClass('last');
                    } 
               });

            }"), array(
            'href' => 'javascript:void(0)',
            'id' => 'latest-products-prev',
            'class' => 'prev'));
        ?>
        
        <?php
        echo CHtml::ajaxLink('&rsaquo;', app()->createUrl('productsale/slideimages'), array(
            'type' => 'POST',
            'datatype' => 'json',
            'data' => array(
                'op'=>'next',
                'firstl' => "js:$('#latest_products ul li:first').attr('id')",
                'lastl' =>  "js:$('#latest_products ul li:last').attr('id')",
                'YII_CSRF_TOKEN' => Yii::app()->getRequest()->getCsrfToken()
                ),
            'success' => "function(data) {
                $('#latest_products ul li').remove();
                var json = $.parseJSON(data);
                $(json).each(function(i,val){
                   $('#latest_products ul').append(
                   '<li id='+val.id+'><div class=\'thumb\'><a href=\'#\'>'+val.image+'</a></div>'
                   +'<div class=\'text-wrap\'><a href=\'#\'>'+val.name+'</a></div></li>');
                   
                });
                var list = $('#latest_products ul li'),
               total = list.length;
               list.each(function(i) {
                    if (i === total - 1) {
                       $(this).addClass('last');
                    } 
               });
            }"), array(
            'href' => 'javascript:void(0)',
            'id' => 'latest-products-next',
            'class' => 'next'));
        ?> 
        
        <!-- end direction navigation -->
    </div>
    
</section>
<!-- end latest_products -->


<?php
//$menus = ProductSaleCategoryList::model()->findAll(array('condition'=>'level=1'));
//
//
// echo CHtml::openTag('ul', array('id' => 'mflo', 'class' => 'mflo'));
// foreach($menus as $menu) {
//    $children = $menu->descendants()->findAll(array('condition'=>'level=3'));
//    echo CHtml::openTag('li', array());
//    echo $menu->name;
//    if(count($children)>0){
//       echo CHtml::openTag('ul', array('class' => 'level-'.$menu->level-1, 'style'=>'margin-left:10px;'));
//       foreach($children as $child){
//           echo CHtml::openTag('li', array());
//           echo $child->name;
//           echo CHtml::closeTag('li');
//       }
//       echo CHtml::closeTag('ul');
//    }
//    
//    echo CHtml::closeTag('li');
// }
// echo CHtml::closeTag('ul');

?>

