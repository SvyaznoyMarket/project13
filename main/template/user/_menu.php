<?
/**
 * @var $page       \View\User\OrderPage
 */
$helper = new \Helper\TemplateHelper();
$route = \App::request()->attributes->get('route');
// $activeLinkCss = 'personalControl_link-active';
$activeMenuCss = 'personalControl_item-active';
?>

<!-- навигация по личному кабинету -->
<nav class="personalControl">
    <li class="personalControl_item <?= in_array($route, ['user.recommend']) ? $activeMenuCss : '' ?>">
        <a href="<?= $helper->url('user.recommend') ?>" class="personalControl_link">Товары для Вас</a>
    </li>

    <li class="personalControl_item <?= in_array($route, ['user.order', 'user.orders']) ? $activeMenuCss : '' ?>">
        <a href="<?= $helper->url('user.orders') ?>" class="personalControl_link">Заказы</a>
    </li>

    <li class="personalControl_item <?= in_array($route, ['user.favorites']) ? $activeMenuCss : '' ?>">
        <a href="<?= $helper->url('user.favorites') ?>" class="personalControl_link">Избранное</a>
    </li>

    <li class="personalControl_item <?= $route == 'user.edit' ? $activeMenuCss : '' ?>">
        <a href="<?= $helper->url('user.edit') ?>" class="personalControl_link">Личные данные</a>
    </li>

    <li class="personalControl_item <?= $route == 'user.subscriptions' ? $activeMenuCss : '' ?>">
        <a href="<?= $helper->url('user.subscriptions') ?>" class="personalControl_link">Подписки</a>
    </li>

    <li class="personalControl_item personalControl_item-text fl-r td-underl">
        <a href="http://my.enter.ru/pravo" target="_blank">cEnter защиты прав потребителей</a>
    </li>
</nav>
<!-- /навигация по личному кабинету -->