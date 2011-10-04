<?php if (false): ?>
<strong><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></strong>
<?php echo $item['creator'] ?>

<?php include_partial('product/price', array('price' => $item['price'])) ?>

<ul class="inline">
  <li><?php include_component('cart', 'buy_button', array('product' => $item['product'])) ?></li>
</ul>
<?php endif ?>
                    <div class="goodsbox">
                        <a href="" class="fastview">Быстрый просмотр</a>
                        <a href="<?php echo url_for('productCard', $item['product'], array('absolute' => true, )) ?>" class="goodsboxlink">
                            <span class="photo"><!--i title="Бестселлер" class="bestseller"></i--><img src="/images/images/photo38.jpg" alt="" title="" width="160" height="160" /></span>
                            <span class="ratingview"></span>
                            <span class="link"><?php echo $item['name'] ?></span>
                            <span class="db font18 pb10"><?php echo $item['price'] ?> <span class="rubl">&#8399;</span></span>
                        </a>
                        <div class="extrabox">
                            <div class="doodsbar">
                                <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
                                <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
                                <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
                            </div>
                        </div>
                    </div>
