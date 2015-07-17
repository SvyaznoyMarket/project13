<?

$config = [
    'slidesToShow' => 4,
    'slidesToScroll' => 4,
    'dots'      => false,
    'infinite'  => false,
    'nextArrow' => '.js-goods-slider-btn-next',
    'prevArrow' => '.js-goods-slider-btn-prev',
    'slider'    => '.js-slider-goods'
];

?>

<div class="section js-module-require" data-module="jquery.slick" data-slick-config='<?= json_encode($config) ?>'>
    <div class="section__title">Ещё у нас покупают</div>

    <div class="section__content">
        <div class="slider-section">
            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev"></button>
            <div class="goods goods_list grid-4col js-slider-goods">
                <div class="goods__item grid-4col__item">
                    <a href="" class="goods__img">
                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                        </div>
                    </div>

                    <div class="goods__cat-count">123 товара</div>
                </div>

                <div class="goods__item grid-4col__item">
                    <a href="" class="goods__img">
                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка</span></a>
                        </div>
                    </div>

                    <div class="goods__cat-count">123 товара</div>
                </div>

                <div class="goods__item grid-4col__item">
                    <a href="" class="goods__img">
                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                        </div>
                    </div>

                    <div class="goods__cat-count">123 товара</div>
                </div>

                <div class="goods__item grid-4col__item">
                    <a href="" class="goods__img">
                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                        </div>
                    </div>

                    <div class="goods__cat-count">123 товара</div>
                </div>

                <div class="goods__item grid-4col__item">
                    <a href="" class="goods__img">
                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка</span></a>
                        </div>
                    </div>

                    <div class="goods__cat-count">123 товара</div>
                </div>

                <div class="goods__item grid-4col__item">
                    <a href="" class="goods__img">
                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                        </div>
                    </div>

                    <div class="goods__cat-count">123 товара</div>
                </div>
            </div>
            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next"></button>
        </div>
    </div>
</div>
