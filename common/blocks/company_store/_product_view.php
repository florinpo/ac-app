<li class="clearfix <?php echo ($index % 2 == 0) ? "first " : "last"; ?>">
<article>
    <div class="thumbnail">
        <?php
        echo Chtml::link($data->selectedImage(180), array(
            'site/store',
            'username' => $data->shop->company->username,
            'page' => 'vendita',
            'prod_id' => $data->id,
            'prod_slug' => $data->slug
        ));
        ?>
    </div>
    <div class="data-wrap">
        <h3>
            <?php
            echo Chtml::link($data->name, array(
                'site/store',
                'username' => $data->shop->company->username,
                'shop_page'=> 'vendita',
                'prod_id' => $data->id,
                'prod_slug' => $data->slug
            ));
            ?>
        </h3>
        <p class="description"><?php echo truncate($data->description, $this->itemwLimit); ?></p>

        <div class="bottom-bar">
            <div class="price">
                <?php if ($data->price > 0): ?>
                    <div class="old-p">
                        <span class="data"><?php echo '500.00'; ?></span>
                        <span class="currency">&euro;</span>
                    </div>
                    <div class="regular">
                        <span class="data"><?php echo $data->price; ?></span>
                        <span class="currency">&euro;</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            echo CHtml::link('<span class="inner"><span class="text">
                 <span class="icon-arrow icon-white"></span>' .
                  t('site', 'Vizualiza') .
                    '</span></span>', array(
                'site/store',
                'username' => $data->shop->company->username,
                'shop_page' => 'vendita',
                'prod_id' => $data->id,
                'prod_slug' => $data->slug), array('class' => 'btn-s green-l', 'id' => 'btn-view'));
            ?>
        </div>
    </div>
</article>
</li>