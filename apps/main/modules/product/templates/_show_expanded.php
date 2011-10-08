<?php if  (false): ?>
<strong><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></strong>
<?php echo $item['creator'] ?>

<?php include_partial('product/price', array('price' => $item['price'])) ?>

<ul class="inline">
  <li><?php include_component('cart', 'buy_button', array('product' => $item['product'])) ?></li>
</ul>

<?php include_component('product', 'property', array('product' => $item['product'])) ?>
<?php endif ?>
            <div class="goodsbox goodsline">
                <a href="" class="fastview">Быстрый просмотр</a>
                <div class="goodsboxlink" onclick="window.location='<?php echo url_for('productCard', $item['product'], array('absolute' => true, )) ?>'">
                    <div class="photo"><!--i title="Бестселлер" class="bestseller"></i--><img src="http://core.ent3.ru/upload/pic/2/200/<?php echo $item['product']['Photo'][0]['resource'] ?>" alt="" title="" width="160" height="160" /></div>
                    <div class="info">
                        <span class="ratingview"></span>
                        <h3><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></h3>
                        <div class="pb5"><?php include_component('product', 'property', array('product' => $item['product'], 'is_list' => true, )); ?></div>
                        <span class="gray">Артикул #<?php echo $item['product']->article ?></span>
                    </div>
                    <div class="extrainfo">
                        <span class="db font18 pb10"><?php echo $item['price'] ?> <span class="rubl">&#8399;</span></span>
                        <ul>
                            <li><strong class="orange">Есть в наличии</strong></li>
                            <li>Доставим в течение 24 часов</li>
                            <li>Поможем настроить смартфон</li>
                        </ul>
                    </div>
                </div>
                <div class="doodsbar">
                    <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
                    <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
                    <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
                </div>
            </div>
