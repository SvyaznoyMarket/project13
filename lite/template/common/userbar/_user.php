<!-- Авторизованным добавлять класс active -->
<li class="user-controls__item user-controls__item_user js-userbar-user">
    <a href="<?= \App::router()->generate('user.login') ?>"
       class="user-controls__link js-popup-show js-module-require-onclick js-userbar-user-link"
       data-popup="login"
       data-module="enter.auth">
        <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
        <span class="user-controls__text js-userbar-user-text" >Войти</span>
    </a>
</li>

<?= $page->blockAuth() ?>