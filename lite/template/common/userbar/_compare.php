<!-- При наличии товаров в сравнении добавлять класс active -->
<li class="user-controls__item user-controls__item_compare">
    <a href="<?= \App::router()->generate('compare') ?>" class="user-controls__link">
        <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
        <span class="js-userbar-compare-counter"></span>
        <span class="user-controls__text">Сравнение</span>
    </a>

    <div class="notice-dd notice-dd_compare" style="display: none">
        <div class="notice-compare">
            <div class="notice-compare__title">Товар добавлен к сравнению</div>

            <div class="notice-compare__img"><img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image"></div>
            <div class="notice-compare__desc">Чехол для Apple iPhone6 XtremeMac Microshield Acc Чехол для App XtremeMac</div>
        </div>
    </div>
</li>