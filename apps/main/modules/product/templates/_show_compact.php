					<div class="goodsbox">
                        <div class="photo"><img src="http://core.ent3.ru/upload/1/163/<?php echo $item['product']['Photo'][0]['resource'] ?>" alt="" title="" width="160" height="160" /></div>
                        <span class="ratingview"></span>
                        <h3><a href="<?php echo url_for('productCard', $item['product']) ?>"><?php echo $item['name'] ?></a></h3>
                        <div class="font18 pb10"><span class="price"><?php echo $item['price'] ?></span></span> <span class="rubl">p</span></div>
                        <!-- Hover -->
                        <div class="boxhover" ref="<?php echo $item['product']->token ?>">
                            <b class="rt"></b><b class="lb"></b>
                            <div class="rb">
                                <div class="lt" >
                                    <a href="" class="fastview">Быстрый просмотр</a>
                                    <div class="photo"><img src="http://core.ent3.ru/upload/1/163/<?php echo $item['product']['Photo'][0]['resource'] ?>" alt="" title="" width="160" height="160" /></div>
                                    <span class="ratingview"></span>
                                    <h3><a href="<?php echo url_for('productCard', $item['product']) ?>"><?php echo $item['name'] ?></a></h3>
                                    <div class="font18 pb10"><span class="price"><?php echo $item['price'] ?></span></span> <span class="rubl">p</span></div>
                                    <div class="goodsbar">
                                <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
                                <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
                                <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Hover -->
                    </div>