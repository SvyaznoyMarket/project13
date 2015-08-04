<?
/**
 * @var $page \View\LiteLayout
 */
?>

<!-- Авторизованным добавлять класс active -->
<li class="user-controls__item user-controls__item_user notice-show js-userbar-user">
    <a href="<?= \App::router()->generate('user.login') ?>"
       class="user-controls__link js-popup-show js-userbar-user-link">
        <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
        <span class="user-controls__text js-userbar-user-text" >Войти</span>
    </a>

    <div class="notice-dd notice-dd_user jsCartNotice">
  		<ul class="notice-user">
  			<li class="notice-user__item"><a href="" class="notice-user__link notice-user__link_lk"><span class="underline">Личный кабинет</span></a></li>
  			<li class="notice-user__item"><a href="" class="notice-user__link notice-user__link_favorite"><span class="underline">Избранное</span></a></li>
  			<li class="notice-user__item"><a href="<?= \App::router()->generate('user.logout') ?>" class="notice-user__link"><span class="underline">Выйти</span></a></li>
  		</ul>
    </div>
</li>

<?= $page->blockAuth() ?>