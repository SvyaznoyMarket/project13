<?
$config = [
    'slidesToShow' => 8,
    'slidesToScroll' => 8,
    'lazyLoad'  => 'ondemand',
    'dots'      => false,
    'infinite'  => false,
    'nextArrow' => '.js-goods-slider-btn-next',
    'prevArrow' => '.js-goods-slider-btn-prev',
    'slider'    => '.js-slider-goods'
];
?>

<div class="section js-module-require js-viewed-products"
     data-module="enter.viewed"
     style="display: none"
     data-slick-config='<?= json_encode($config) ?>'>
    <div class="section__title">Вы смотрели</div>

    <div class="section__content" style="position: relative">
        <div class="slider-section slider-section_100">
            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev"></button>
            <div class="viewed-goods goods goods_images goods_list grid-8col js-slider-goods js-viewed-products-inner">
            </div>
            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next"></button>
        </div>
    </div>
</div>

<script type="text/plain" class="js-product-viewed-template">
{{#products}}
    <div class="goods__item grid-8col__item">
        <a href="{{productUrl}}" class="goods__img" title="{{name}}">
            <img data-lazy="{{imageUrl}}" alt="{{name}}" class="goods__img-image">
        </a>

        <div class="viewed-goods-name">
            <span class="viewed-goods-name__value">{{name}}</span>
        </div>
    </div>
{{/products}}
</script>