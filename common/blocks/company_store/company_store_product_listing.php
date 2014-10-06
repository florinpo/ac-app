<div class="grid_15 box_shop_content">

    <?php
    $this->widget('zii.widgets.CMenu', array(
        'id' => 'tabnav-shop',
        'items' =>$this->menu
    ));
    ?>
    <div class="tabnav-shop-body">
        <section id="product_results" class="shop-page">
            <?php if ($total > 0): ?>
                <h2><?php echo $total . t('site', ' prodotti trovati'); ?></h2>
                <?php
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider' => $model,
                    'itemView' => 'common.blocks.company_store._product_view',
                    'template' => '{summary}{items}{pager}',
                    'summaryText' => t('site', 'Pagina ') . '{page}' . t('site', ' da ') . '{pages}',
                    'pager' => array(
                        'header' => '',
                        'nextPageLabel' => t('site', 'successivo'),
                        'prevPageLabel' => t('site', 'precedente'),
                        'maxButtonCount' => 5,
                    ),
                    'id' => 'items-shop-single',
                    'itemsTagName' => 'ul',
                    'itemsCssClass' => 'items-list clearfix',
                    'pagerCssClass' => 'pager-classic',
                    'ajaxUpdate' => false,
                    'enablePagination' => true,
                    'enableSorting' => false,
                ));
                ?>
            <?php else: ?>
                <h2><?php echo $total . t('site', ' risultati trovati'); ?></h2>
                <div class="empty_results">
                    <ul>
                        <li><?php echo t('site', 'Va rugam sa verificati cuvantul cautat (minim 2 caractere)'); ?></li>
                        <li><?php echo t('site', 'Folositi un cuvant mai general'); ?></li>
                        <li><?php echo t('site', 'Va rugam folositi maxim 10 cuvinte la cautare'); ?></li>
                    </ul>
                </div>
            <?php endif; ?>

        </section>
    </div>

</div>

