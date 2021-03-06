<?
/**
 * @var $page       \View\User\OrderPage
 */
$helper = new \Helper\TemplateHelper();
$route = \App::request()->routeName;
// $activeLinkCss = 'personalControl_link-active';
$activeMenuCss = 'active';
?>

<!-- навигация по личному кабинету -->
<div class="personal__menu">
    <ul class="personal-navs">
        <li class="personal-navs__i <?= in_array($route, ['user']) ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user') ?>" class="personal-navs__lk">Моё всё</a>
        </li>
        <li class="personal-navs__i <?= in_array($route, ['user.order', 'user.orders']) ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user.orders') ?>" class="personal-navs__lk">Заказы</a>
        </li>
        <li class="personal-navs__i <?= in_array($route, ['user.favorites']) ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user.favorites') ?>" class="personal-navs__lk">Избранное</a>
        </li>
        <li class="personal-navs__i <?= $route == 'user.subscriptions' ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user.subscriptions') ?>"  class="personal-navs__lk">Подписки</a>
        </li>
        <li class="personal-navs__i <?= $route == 'user.address' ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user.address') ?>"  class="personal-navs__lk">Адреса</a>
        </li>
        <li class="personal-navs__i <?= $route == 'user.message' ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user.message') ?>"  class="personal-navs__lk">Сообщения</a>
        </li>
        <li class="personal-navs__i <?= $route == 'user.edit' ? $activeMenuCss : '' ?>">
            <a href="<?= $helper->url('user.edit') ?>" class="personal-navs__lk">Личные данные</a>
        </li>
    </ul>
</div>
<!-- /навигация по личному кабинету -->