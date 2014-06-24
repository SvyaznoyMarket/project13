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

<div class="personalOrders">
    <div class="personalOrders_row personalOrders_row-head">
        <div class="personalOrders_cell">Дата</div>
        <div class="personalOrders_cell">№ заказа</div>
        <div class="personalOrders_cell">Состав</div>
        <div class="personalOrders_cell">Сумма</div>
        <div class="personalOrders_cell">Получение</div>
        <div class="personalOrders_cell">Резерв до</div>
        <div class="personalOrders_cell">Статус</div>
        <div class="personalOrders_cell"></div>
    </div>

    <div class="personalOrders_row">
        <div class="personalOrders_cell">06.06.14</div>
        <div class="personalOrders_cell"><a href="">COXD-305176</a></div>
        <div class="personalOrders_cell personalOrders_cell-text">
            <ul class="orderItem">
                <li>Бумажный конструктор 3 шт.</li>
                <li>Карта памяти microSDHC… 1 шт.</li>
                <li><a href="">и ещё 3 товара</a></li>
            </ul>
        </div>
        <div class="personalOrders_cell personalOrders_cell-right">46 740 <span class="rubl">p</span></div>
        <div class="personalOrders_cell">Доставка</div>
        <div class="personalOrders_cell">23:59 11.07.14</div>
        <div class="personalOrders_cell">Принят</div>
        <div class="personalOrders_cell"><a class="orderCancel" href="">Отменить</a></div>
    </div>

    <div class="personalOrders_row">
        <div class="personalOrders_cell">06.06.14</div>
        <div class="personalOrders_cell"><a href="">COXD-305176</a></div>
        <div class="personalOrders_cell personalOrders_cell-text">
            Бумажный конструктор 3 шт.
        </div>
        <div class="personalOrders_cell personalOrders_cell-right">740 <span class="rubl">p</span></div>
        <div class="personalOrders_cell">Самовывоз</div>
        <div class="personalOrders_cell"></div>
        <div class="personalOrders_cell">Принят</div>
        <div class="personalOrders_cell"><a class="orderCancel" href="">Отменить</a></div>
    </div>
</div>

<div class="personalTitle">История</div>

<div class="personalOrders">
    <div class="personalOrders_row personalOrders_row-head personalOrders_row-center">
        <div class="personalOrders_cell">Дата</div>
        <div class="personalOrders_cell">№ заказа</div>
        <div class="personalOrders_cell">Состав</div>
        <div class="personalOrders_cell">Сумма</div>
        <div class="personalOrders_cell">Получение</div>
        <div class="personalOrders_cell">Статус</div>
        <div class="personalOrders_cell"></div>
    </div>

    <div class="personalOrders_row">
        <div class="personalOrders_cell">06.06.14</div>
        <div class="personalOrders_cell"><a href="">COXD-305176</a></div>
        <div class="personalOrders_cell personalOrders_cell-text">
            <ul class="orderItem">
                <li>Бумажный конструктор 3 шт.</li>
                <li>Карта памяти microSDHC… 1 шт.</li>
                <li><a href="">и ещё 3 товара</a></li>
            </ul>
        </div>
        <div class="personalOrders_cell personalOrders_cell-right">
            46 740 <span class="rubl">p</span><br/>
            <span class="textStatus">оплачено</span>
        </div>
        <div class="personalOrders_cell">Доставка</div>
        <div class="personalOrders_cell">Принят</div>
        <div class="personalOrders_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>

    <div class="personalOrders_row">
        <div class="personalOrders_cell">06.06.14</div>
        <div class="personalOrders_cell"><a href="">COXD-305176</a></div>
        <div class="personalOrders_cell personalOrders_cell-text">
            Бумажный конструктор 3 шт.
        </div>
        <div class="personalOrders_cell personalOrders_cell-right">
            740 <span class="rubl">p</span><br/>
            <span class="textStatus">оплачено</span>
        </div>
        <div class="personalOrders_cell">Самовывоз</div>
        <div class="personalOrders_cell">Принят</div>
        <div class="personalOrders_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>

    <div class="personalOrders_row personalOrders_row-canceled">
        <div class="personalOrders_cell personalOrders_cell-strong">06.06.14</div>
        <div class="personalOrders_cell personalOrders_cell-strong"><a href="">COXD-305176</a></div>
        <div class="personalOrders_cell personalOrders_cell-text personalOrders_cell-strong">
            <ul class="orderItem">
                <li>Бумажный конструктор 3 шт.</li>
                <li>Карта памяти microSDHC… 1 шт.</li>
                <li><a href="">и ещё 3 товара</a></li>
            </ul>
        </div>
        <div class="personalOrders_cell personalOrders_cell-strong personalOrders_cell-right">46 740 <span class="rubl">p</span></div>
        <div class="personalOrders_cell personalOrders_cell-strong">Доставка</div>
        <div class="personalOrders_cell">Отменён</div>
        <div class="personalOrders_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>

    <div class="personalOrders_row">
        <div class="personalOrders_cell">06.06.14</div>
        <div class="personalOrders_cell"><a href="">COXD-305176</a></div>
        <div class="personalOrders_cell personalOrders_cell-text">
            Бумажный конструктор 3 шт.
        </div>
        <div class="personalOrders_cell personalOrders_cell-right">
            40 <span class="rubl">p</span>
            <span class="textStatus">оплачено</span>
        </div>
        <div class="personalOrders_cell">Самовывоз</div>
        <div class="personalOrders_cell">Принят</div>
        <div class="personalOrders_cell"><a class="orderCancel" href="">Добавить в корзину</a></div>
    </div>
</div>