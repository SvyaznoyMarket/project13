<?php
/**
 * @var $page       \View\User\IndexPage
 * @var $user       \Session\User
 * @var $orderCount int
 */
?>

<menu class="personalControl">
    <a href="" class="personalControl_link personalControl_link-active">Заказы</a>
    <a href="" class="personalControl_link">Избранное</a>
    <a href="" class="personalControl_link">Сравнение</a>
    <a href="" class="personalControl_link">Личные данные</a>
    <a href="" class="personalControl_link">Подписки e-mail и sms</a>
    <a href="" class="personalControl_link">Фишки Enter Prize</a>
</menu>

<div class="personalTitle">Текущие заказы <span class="personalTitle_count">3</span></div>

<div class="personalTable personalTable-border">
    <div class="personalTable_row personalTable_row-head">
        <div class="personalTable_cell">Дата</div>
        <div class="personalTable_cell">№ заказа</div>
        <div class="personalTable_cell">Состав</div>
        <div class="personalTable_cell">Сумма</div>
        <div class="personalTable_cell">Получение</div>
        <div class="personalTable_cell">Резерв до</div>
        <div class="personalTable_cell">Статус</div>
        <div class="personalTable_cell"></div>
    </div>

    <div class="personalTable_row">
        <div class="personalTable_cell">06.06.14</div>
        <div class="personalTable_cell"><a href="">COXD-305176</a></div>
        <div class="personalTable_cell personalTable_cell-text">
            <ul class="orderItem">
                <li>Бумажный конструктор 3 шт.</li>
                <li>Карта памяти microSDHC… 1 шт.</li>
                <li><a href="">и ещё 3 товара</a></li>
            </ul>
        </div>
        <div class="personalTable_cell personalTable_cell-right">46 740 <span class="rubl">p</span></div>
        <div class="personalTable_cell">Доставка</div>
        <div class="personalTable_cell">23:59 11.07.14</div>
        <div class="personalTable_cell">Принят</div>
        <div class="personalTable_cell"><a class="orderCancel" href="">Отменить</a></div>
    </div>

    <div class="personalTable_row">
        <div class="personalTable_cell">06.06.14</div>
        <div class="personalTable_cell"><a href="">COXD-305176</a></div>
        <div class="personalTable_cell personalTable_cell-text">
            Бумажный конструктор 3 шт.
        </div>
        <div class="personalTable_cell personalTable_cell-right">740 <span class="rubl">p</span></div>
        <div class="personalTable_cell">Самовывоз</div>
        <div class="personalTable_cell"></div>
        <div class="personalTable_cell">Принят</div>
        <div class="personalTable_cell"><a class="orderCancel" href="">Отменить</a></div>
    </div>
</div>

<div class="personalTitle">История</div>

<div class="personalTable personalTable-border">
    <div class="personalTable_row personalTable_row-head personalTable_row-center">
        <div class="personalTable_cell">Дата</div>
        <div class="personalTable_cell">№ заказа</div>
        <div class="personalTable_cell">Состав</div>
        <div class="personalTable_cell">Сумма</div>
        <div class="personalTable_cell">Получение</div>
        <div class="personalTable_cell">Статус</div>
        <div class="personalTable_cell"></div>
    </div>

    <div class="personalTable_row">
        <div class="personalTable_cell">06.06.14</div>
        <div class="personalTable_cell"><a href="">COXD-305176</a></div>
        <div class="personalTable_cell personalTable_cell-text">
            <ul class="orderItem">
                <li>Бумажный конструктор 3 шт.</li>
                <li>Карта памяти microSDHC… 1 шт.</li>
                <li><a href="">и ещё 3 товара</a></li>
            </ul>
        </div>
        <div class="personalTable_cell personalTable_cell-right">
            46 740 <span class="rubl">p</span><br/>
            <span class="textStatus">оплачено</span>
        </div>
        <div class="personalTable_cell">Доставка</div>
        <div class="personalTable_cell">Принят</div>
        <div class="personalTable_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>

    <div class="personalTable_row">
        <div class="personalTable_cell">06.06.14</div>
        <div class="personalTable_cell"><a href="">COXD-305176</a></div>
        <div class="personalTable_cell personalTable_cell-text">
            Бумажный конструктор 3 шт.
        </div>
        <div class="personalTable_cell personalTable_cell-right">
            740 <span class="rubl">p</span><br/>
            <span class="textStatus">оплачено</span>
        </div>
        <div class="personalTable_cell">Самовывоз</div>
        <div class="personalTable_cell">Принят</div>
        <div class="personalTable_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>

    <div class="personalTable_row personalTable_row-canceled">
        <div class="personalTable_cell personalTable_cell-strong">06.06.14</div>
        <div class="personalTable_cell personalTable_cell-strong"><a href="">COXD-305176</a></div>
        <div class="personalTable_cell personalTable_cell-text personalTable_cell-strong">
            <ul class="orderItem">
                <li>Бумажный конструктор 3 шт.</li>
                <li>Карта памяти microSDHC… 1 шт.</li>
                <li><a href="">и ещё 3 товара</a></li>
            </ul>
        </div>
        <div class="personalTable_cell personalTable_cell-strong personalTable_cell-right">46 740 <span class="rubl">p</span></div>
        <div class="personalTable_cell personalTable_cell-strong">Доставка</div>
        <div class="personalTable_cell">Отменён</div>
        <div class="personalTable_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>

    <div class="personalTable_row">
        <div class="personalTable_cell">06.06.14</div>
        <div class="personalTable_cell"><a href="">COXD-305176</a></div>
        <div class="personalTable_cell personalTable_cell-text">
            Бумажный конструктор 3 шт.
        </div>
        <div class="personalTable_cell personalTable_cell-right">
            40 <span class="rubl">p</span>
            <span class="textStatus">оплачено</span>
        </div>
        <div class="personalTable_cell">Самовывоз</div>
        <div class="personalTable_cell">Принят</div>
        <div class="personalTable_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>
</div>

<div class="personalTitle"><a href="">Текущие заказы</a> <span class="personalTitle_count">3</span></div>

<div class="personalPage_left">
    <div class="personalPage_head">
        <h2 class="personalPage_head_left orderTitle_left-mark">Заказ COXD-305176 от 06.06.14</h2>

        <div class="personalPage_head">К оплате: <span class="orderPrice">42 740 <span class="rubl">p</span></span></div>
    </div>

    <ul class="personalControl">
        <li class="personalControl_link personalControl_link-active">Принят</li>
        <li class="personalControl_link">Отправлен</li>
        <li class="personalControl_link">Получен</li>
    </ul>

    <div class="personalInfo">
        <p>Плановый срок получения заказа <mark class="decorColor">Завтра, 15 июня, воскресенье</mark></p>
        
        <div class="personalTable">
            <div class="personalTable_row">
                <div class="personalTable_cell">Магазин</div>

                <div class="personalTable_cell personalTable_cell-s">
                    <mark class="decorColor decorColor-bullet" style="color: #b1b4c2"><span class="decorColor">м. Петровско-Разумовская</span></mark>
                    ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2
                </div>

                <div class="personalTable_cell personalTable_cell-s">
                    <mark class="decorColor">режим работы</mark><br/>
                    с 9.00 до 22.00
                </div>

                <div class="personalTable_cell"><a class="decorColor">Как добраться?</a></div>
            </div>
        </div>

        <p>В магазине можно оплатить заказ как наличными, так и банковской картой.</p>
    </div>

    <div class="personalTable personalTable-border nomargin">
        <div class="personalTable_row">
            <div class="personalTable_cell personalTable_cell-w40">
                <img class="imgProd" src="http://fs10.enter.ru/1/1/120/4b/260016.jpg" alt="" />
            </div>

            <div class="personalTable_cell">
                Бумажный конструктор<br/> 
                Jazwares Minecraft Papercraft «Дружелюбные мобы»
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-w60 personalTable_cell-l personalTable_cell-strong">
                200 <span class="rubl">p</span><br/>
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-w60 personalTable_cell-l personalTable_cell-strong">2 шт.</div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-l">
                400 <span class="rubl">p</span><br/>
            </div>
        </div>

        <div class="personalTable_row">
            <div class="personalTable_cell personalTable_cell-w40">
                <img class="imgProd" src="http://fs10.enter.ru/1/1/120/4b/260016.jpg" alt="" />
            </div>

            <div class="personalTable_cell">
                Бумажный конструктор<br/> 
                Jazwares Minecraft Papercraft «Дружелюбные мобы»
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-w60 personalTable_cell-l personalTable_cell-strong">
                1 200 <span class="rubl">p</span><br/>
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-w60 personalTable_cell-l personalTable_cell-strong">2 шт.</div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-l">
                400 <span class="rubl">p</span><br/>
            </div>
        </div>

        <div class="personalTable_row">
            <div class="personalTable_cell personalTable_cell-w40">
                <img class="imgProd" src="http://fs10.enter.ru/1/1/120/4b/260016.jpg" alt="" />
            </div>

            <div class="personalTable_cell">
                Бумажный конструктор<br/> 
                Jazwares Minecraft Papercraft «Дружелюбные мобы»
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-w60 personalTable_cell-l personalTable_cell-strong">
                12 200 <span class="rubl">p</span><br/>
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-w60 personalTable_cell-l personalTable_cell-strong">2 шт.</div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-l">
                400 <span class="rubl">p</span><br/>
            </div>
        </div>
    </div>

    <div class="personalTable personalTable-border">
        <div class="personalTable_caption">
            Скидки
        </div>

        <div class="personalTable_row">
            <div class="personalTable_cell personalTable_cell-w40">
                <img class="imgProd" src="/styles/personal-page/img/enterLogo.png" alt="" />
            </div>

            <div class="personalTable_cell">
                Фишка со скидкой 2% на категорию Электроника<br/> 
                Минимальная сумма заказа 6999 руб
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-l personalTable_cell-warn">
                - 400 <span class="rubl">p</span><br/>
            </div>
        </div>

        <div class="personalTable_row">
            <div class="personalTable_cell personalTable_cell-w40">
                <img class="imgProd" src="/styles/personal-page/img/fishka.png" alt="" />
            </div>

            <div class="personalTable_cell">
                Подарочный сертификат 5000 руб
            </div>

            <div class="personalTable_cell personalTable_cell-right personalTable_cell-l personalTable_cell-warn">
                - 2 400 <span class="rubl">p</span><br/>
            </div>
        </div>
    </div>
</div>

<aside class="personalPage_right">
    <menu class="payCommands">
        <ul class="payCommandsList">
            <li class="payCommandsList_item">
                <button class="btnPay">Оплатить баллами</button>

                <span class="descPay">
                    <img src="/styles/personal-page/img/cards/sclub.png" alt="" class="descPay_img" />
                    <img src="/styles/personal-page/img/cards/sber.png" alt="" class="descPay_img" />
                </span>
            </li>
            <li class="payCommandsList_item">
                <button class="btnPay">Оплатить онлайн</button>

                <span class="descPay">
                    <img src="/styles/personal-page/img/cards/MasterCard.png" alt="" class="descPay_img" />
                    <img src="/styles/personal-page/img/cards/Visa.png" alt="" class="descPay_img" />
                    <img src="/styles/personal-page/img/cards/Maestro.png" alt="" class="descPay_img" />
                    <img src="/styles/personal-page/img/cards/paypal.png" alt="" class="descPay_img" />
                    <img src="/styles/personal-page/img/cards/psb.png" alt="" class="descPay_img" />
                </span>
            </li>
        </ul>
    </menu>
</aside>








