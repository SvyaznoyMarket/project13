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

<div class="personalTable">
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

<div class="personalTable">
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
        Плановый срок получения заказа Завтра, 15 июня, воскресенье

        Магазин 

        м. Петровско-Разумовская ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2

        режим работы
        с 9.00 до 22.00

        Как добраться?

        В магазине можно оплатить заказ как наличными, так и банковской картой.
    </div>

    <div class="personalTable">
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
</div>


<aside class="personalPage_right"></aside>








