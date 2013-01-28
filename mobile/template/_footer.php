<?php
/**
 * @var $page \Mobile\View\DefaultLayout
 */
?>

<footer class="bFooter"><!-- футер страницы -->
    <div class="bLightGrayWrap">
        <a class="bContactPhone mDisplayBlock" href="tel:88007000009">Контакт cENTER <strong class="bContactPhone_ePhone"><?= \App::config()->company['phone'] ?></strong></a>
    </div>
    <nav class="bBottomLinks clearfix">
        <a class="bBottomLinks_eLink" href="/special_offers">Акции</a>
        <a class="bBottomLinks_eLink" href="/how_make_order">Как сделать заказ</a>
        <a class="bBottomLinks_eLink" href="<?= $page->url('shop') ?>">Наши магазины</a>
        <a class="bBottomLinks_eLink" href="<?= $page->url('user') ?>">Личный кабинет</a>
        <a class="bBottomLinks_eLink" href="<?= $page->url('service') ?>">Сервис F1</a>
        <a class="bBottomLinks_eLink" href="/mobile_apps">Приложение Android</a>
        <a class="bBottomLinks_eLink" href="/about_company">О комании</a>
        <a class="bBottomLinks_eLink" href="<?= \App::config()->mainHost ?><?= $page->url('homepage') ?>">Полная версия</a>
    </nav>
    <aside class="bCopyright">&copy; ООО «Энтер» 2011–2013. ENTER® ЕНТЕР® Enter®. Все права защищены.</aside>
</footer>
