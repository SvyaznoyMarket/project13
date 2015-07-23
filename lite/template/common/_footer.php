<?
/**
 * @var $page \View\LiteLayout
 */
?>

<div class="footer">
    <div class="footer__right">
        <ul class="footer-external">
            <li class="footer-external__item footer-external__item_title">Оставайтесь на связи</li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_fb"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_od"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_tw"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_vk"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_yt"></i></a></li>
        </ul>

        <ul class="footer-external">
            <li class="footer-external__item footer-external__item_title">Мобильные приложения</li>

            <li class="footer-external__item">
                <a href="" class="footer-external__link">
                    <span class="app-box app-box_apple">Загрузите<br/> в App Store</span>
                </a>
            </li>

            <li class="footer-external__item">
                <a href="" class="footer-external__link">
                    <span class="app-box app-box_android">Загрузите<br/> на Google Play</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="footer__left">
        <ul class="footer-list grid-4col">
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">О компании</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Работа у нас</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Правовая информация</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Уцененные товары оптом</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Способы оплаты</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Обратная связь</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Условия продажи</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Рекламные возможности</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Покупка в кредит</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">ЦСИ</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Информация о СМИ</a></li>
            <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Партнерам</a></li>
        </ul>

        <form action="" class="subscribe-form">
            <div class="subscribe-form__title">Подписаться на рассылку и получить 300 <span class="rubl-css">P</span> на следующую покупку</div>
            <input type="text" class="subscribe-form__it it" placeholder="Ваш email">
            <button class="subscribe-form__btn btn-normal">Подписаться</button>
        </form>

        <div class="footer-hint">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</div>

        <ul class="footer-external footer-external_fl-r">
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-partner i-partner_mnogoru"></i></a></li>
            <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-partner i-partner_sb"></i></a></li>
        </ul>
    </div>
</div>

<footer class="copy">
    <div class="inner">
        <div class="copy__left">&copy; ООО «Энтер» 2011–2014. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
        <div class="copy__right"><a href="">Сообщить об ошибке</a></div>
        <div class="copy__center"><a href="">Мобильная версия сайта</a></div>
    </div>
</footer>

<?= $page->blockModulesDefinitions() ?>