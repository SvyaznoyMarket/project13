<?
/**
 * @var $page \View\Cart\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<?= $page->blockHeader() ?>

<div class="wrapper">
    <main class="content">
        <div class="section js-cart js-cart-page">
            <div class="section__title section__title_h1">Корзина</div>

            <!-- пустая корзина -->
            <div class="cart-empty">
                <div class="cart-empty__title">В корзине нет товаров</div>
                <div class="cart-empty__desc">
                    Попробуйте <a href="" class="cart-empty__search"><span class="underline">поиск</span></a>
                </div>

                <div class="cart-empty__desc">
                    или посмотрите наши <a href="" class="cart-empty__wow"><span class="underline">акции</span></a>
                </div>
            </div>
            <!--/ пустая корзина -->

            <div class="cart table js-cart-items-wrapper"></div>

            <!-- общая сумма заказа -->
            <div class="cart-summ">
               <div class="cart-summ__title">Сумма заказа:</div>
               <div class="cart-summ__price"><span class="js-cart-sum"></span>&thinsp;<span class="rubl">C</span></div>
            </div>
            <!--/ общая сумма заказа -->

            <div class="cart-order">
               <div class="cart-order__back"><a class="cart-order__back-link" href="<?= \App::router()->generate('homepage') ?>" title=""><span class="underline">Вернуться к покупкам</span></a></div>
               <div class="cart-order__btn"><a href="<?= \App::router()->generate('orderV3') ?>" class="btn-primary btn-primary_bigger btn-primary_width">Оформить заказ</a></div>
            </div>
        </div>

        <?= $page->blockViewed() ?>
    </main>
</div>

<script type="text/html" id="js-cart-page-item-template">
    <div class="cart__row table-row">
       <div class="cart__image cart__cell table-cell"><a href="{{link}}"><img src="{{img}}" alt="" class="image"></a></div>

       <div class="cart__desc cart__cell table-cell">
            <div class="cart__product-name"><a href="{{link}}">{{name}}</a></div>
            <div class="cart__product-price">{{formattedPrice}}&thinsp;<span class="rubl">B</span></div>
       </div>

       <div class="cart__counter cart__cell table-cell">
            <div class="counter js-counter">
                <button class="counter__btn counter__btn_minus disabled js-counter-minus"></button>
                <input type="text" class="counter__it js-counter-value" value="{{quantity}}">
                <button class="counter__btn counter__btn_plus js-counter-plus"></button>
                <span class="counter__num">шт.</span>
            </div>

            <a class="cart__delete js-cart-item-delete" href="{{deleteUrl}}"><span class="dotted">Удалить</span></a>
       </div>

       <div class="cart__summ cart__cell table-cell">{{formattedFullPrice}}&thinsp;<span class="rubl">B</span></div>
    </div>
</script>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

<?= $page->blockPopupTemplates() ?>

</body>
</html>
