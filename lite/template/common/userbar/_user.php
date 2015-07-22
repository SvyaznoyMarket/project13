<!-- Авторизованным добавлять класс active -->
<li class="user-controls__item user-controls__item_user">
    <a href="" class="user-controls__link js-popup-show js-module-require-onclick" data-popup="login" data-module="enter.auth">
        <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
        <span class="user-controls__text" >Войти</span>
    </a>
</li>

<?= $page->blockAuth() ?>