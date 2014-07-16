<?php

return function(
    \Helper\TemplateHelper $helper
) {

?>

<?= $helper->render('order-v3/__head', ['step' => 3]) ?>

    <section class="orderCnt">
        <h1 class="orderCnt_t">Заказы оформлены</h1>

        <p>
            Вы получите смс с номерами заказов. <br/>
            С вами свяжется курьер для уточнения удобного для вас времени доставки.
        </p>

        <p>
            Вы можете оплатить свой заказ онлайн.
            <img src="/styles/order/img/master.png" alt="" />
            <img src="/styles/order/img/Visa.png" alt="" />
            <img src="/styles/order/img/Maestro.png" alt="" />
            <img src="/styles/order/img/paypal.png" alt="" />
            <img src="/styles/order/img/psb.png" alt="" />
        </p>

        <p>При получении заказа всегда принимаем наличные. </p>

        <!-- таблица текущих заказов -->
        <div class="personalTable personalTable-border personalTable-bg">
            <div class="personalTable_row personalTable_row-head">
                <div class="personalTable_cell personalTable_cell-w90">№ заказа</div>

                <div class="personalTable_cell personalTable_cell-w212">Состав</div>

                <div class="personalTable_cell personalTable_cell-w115 ta-c">Сумма</div>

                <div class="personalTable_cell personalTable_cell-w175">Получение</div>

                <div class="personalTable_cell">Статус</div>

                <div class="personalTable_cell"></div>
            </div>

            <div class="personalTable_row">
                <div class="personalTable_cell ta-c">
                    <a href="">COXD-305176</a>
                    <span class="s dblock">06 июн. 2014</span>
                </div>

                <div class="personalTable_cell personalTable_cell-text">
                    <ul class="orderItem">
                        <li>Бумажный конструктор 3 шт.</li>
                        <li>Карта памяти microSDHC… 1 шт.</li>
                        <li><a href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="personalTable_cell ta-r">
                    46 740 <span class="rubl">p</span>
                    <strong class="s dblock">Заказ оплачен</strong>
                </div>

                <div class="personalTable_cell">
                    Доставка
                    <span class="s dblock">11 июл. 2014 9:00…18:00</span>
                </div>

                <div class="personalTable_cell">В обработке</div>

                <div class="personalTable_cell"></div>
            </div>

            <!-- ----------------- -->

            <div class="personalTable_row">
                <div class="personalTable_cell ta-c">
                    <a href="">COXD-305176</a>
                    <span class="s dblock">06 июн. 2014</span>
                </div>

                <div class="personalTable_cell personalTable_cell-text">
                    <ul class="orderItem">
                        <li>Бумажный конструктор 3 шт.</li>
                        <li>Карта памяти microSDHC… 1 шт.</li>
                        <li><a href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="personalTable_cell ta-r">
                    46 740 <span class="rubl">p</span>
                    <span class="s dblock">Оплачено: <span class="m">43 <span class="rubl">p</span></span></span>
                    <span class="s dblock">К оплате: <span class="m">434 <span class="rubl">p</span></span></span>
                </div>

                <div class="personalTable_cell">
                    Доставка
                    <span class="s dblock">11 июл. 2014 9:00…18:00</span>
                </div>

                <div class="personalTable_cell">Готов к передаче</div>

                <div class="personalTable_cell"></div>
            </div>

            <!-- ----------------- -->

            <div class="personalTable_row">
                <div class="personalTable_cell ta-c">
                    <a href="">COXD-305176</a>
                    <span class="s dblock">06 июн. 2014</span>
                </div>

                <div class="personalTable_cell personalTable_cell-text">
                    <ul class="orderItem">
                        <li>Бумажный конструктор 3 шт.</li>
                        <li>Карта памяти microSDHC… 1 шт.</li>
                        <li><a href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="personalTable_cell ta-r">
                    46 740 <span class="rubl">p</span>
                    <strong class="s dblock">Покупка в кредит</strong>
                    <span class="s dblock">К оплате: <span class="m">434 <span class="rubl">p</span></span></span>
                </div>

                <div class="personalTable_cell">
                    Доставка
                    <span class="s dblock">11 июл. 2014 9:00…18:00</span>
                </div>

                <div class="personalTable_cell">В обработке</div>

                <div class="personalTable_cell personalTable_cell-last personalTable_cell-mark ta-r">
                    <button class="tableBtn btnLightGrey s">Заполнить заявку<br/>на кредит</button>
                </div>
            </div>

            <!-- ----------------- -->

            <div class="personalTable_row">
                <div class="personalTable_cell ta-c">
                    <a href="">COXD-305176</a>
                    <span class="s dblock">06 июн. 2014</span>
                </div>

                <div class="personalTable_cell personalTable_cell-text">
                    <ul class="orderItem">
                        <li>Бумажный конструктор 3 шт.</li>
                        <li>Карта памяти microSDHC… 1 шт.</li>
                        <li><a href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="personalTable_cell ta-r">
                    46 740 <span class="rubl">p</span>
                </div>

                <div class="personalTable_cell">
                    Доставка
                    <span class="s dblock">11 июл. 2014 9:00…18:00</span>
                </div>

                <div class="personalTable_cell">В обработке</div>

                <div class="personalTable_cell"></div>
            </div>
        </div>
        <!--/ таблица текущих заказов -->
    </section>

<? };

