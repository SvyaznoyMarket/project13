<!-- При наличии товаров в сравнении добавлять класс active -->
<li class="user-controls__item user-controls__item_compare">
    <a href="<?= \App::router()->generate('compare') ?>" class="user-controls__link">
        <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
        <span class="user-controls__text">Сравнение</span>
        <span class="user-controls__count js-userbar-compare-counter"></span>
    </a>

    <div class="notice-dd notice-dd_compare js-userbar-compare-dd" style="display: none"></div>
</li>


<script type="text/plain" id="js-userbar-comparing-item">
    <div class="notice-compare">
        <div class="notice-compare__title">Товар добавлен к сравнению</div>

        <div class="notice-compare__img"><img src="{{imageUrl}}" alt="" class="image"></div>
        <div class="notice-compare__desc">{{prefix}} {{webName}}</div>
    </div>
</script>