<?
/**
 * @var $page \View\LiteLayout
 */
?>

<footer class="footer">
    <ul class="footer-list">
        <li class="footer-list__item"><a href="/delivery-types" class="footer-list__link underline">Доставка</a></li>
        <li class="footer-list__item"><a href="/delivery" class="footer-list__link underline">Самовывоз</a></li>
        <li class="footer-list__item"><a href="/payment" class="footer-list__link underline">Оплата</a></li>
        <li class="footer-list__item"><a href="/about-company" class="footer-list__link underline">О компании</a></li>
        <li class="footer-list__item"><a href="/privacy-policy" class="footer-list__link underline">Правовая информация</a></li>
        <li class="footer-list__item"><a href="/offer" class="footer-list__link underline">Оферта</a></li>
        <li class="footer-list__item footer-list__item_feedback"><a href="" class="btn-normal js-feedback-from-btn">Обратная связь</a></li>
    </ul>

    <ul class="footer__right footer-external" style="display: none">
        <li class="footer-external__item footer-external__item_title">Мы в социальных сетях</li>
        <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_fb"></i></a></li>
        <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_od"></i></a></li>
        <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_tw"></i></a></li>
        <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_vk"></i></a></li>
        <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_yt"></i></a></li>
    </ul>

    <div class="footer__left">
        <div class="footer-hint">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</div>
        <div class="footer-copy">&copy; ООО «Сордекс» 2013–<?= date("Y"); ?>. Все права защищены.</div>
    </div>
</footer>

<?= $page->blockModulesDefinitions() ?>