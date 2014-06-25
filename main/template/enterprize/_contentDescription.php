<?php
/**
 * @var $page \View\Layout
 */
?>

<p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как получить больше фишек?</span></p>

<div class="enterPrizeListWrap">
    <ul class="enterPrizeList">
        <li class="enterPrizeList__item mBlue">
            <strong>Сайт www.enter.ru</strong><br>
            Всегда входите в личный кабинет.<br>
            Заказывайте товары как обычно.
        </li>

        <li class="enterPrizeList__item mOrange">
            <strong>Розничные магазины ENTER</strong><br>
            Входите в личный кабинет в терминале.<br>
            Заказывайте товары через терминал.
        </li>

        <li class="enterPrizeList__item mGreen">
            <strong>Контакт-сENTER 8 800 700 00 09</strong><br>
            Скажите оператору Контакт-cENTER, что Вы &mdash; участник Enter Prize!<br>
            Оператор поможет оформить заказ.
        </li>
    </ul>

    <div class="enterPrizeFinish">Ловите номер фишки в чеке после оплаты заказа!</div>
</div>

<p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как воспользоваться кодом фишки и получить скидку?</span></p>

<div class="enterPrizeListWrap">
    <?= $page->render('enterprize/__contentHowToGetDiscount') ?>
</div>