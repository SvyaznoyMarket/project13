<?
/**
 * @var $points Model\Point\ScmsPoint[]
 * @var $partners []
 * @var $partnersBySlug []
 * @var $objectManagerData []
 * @var $page View\Content\DeliveryMapPage
 */
$helper = \App::helper();
?>

<?= $helper->jsonInScriptTag($partnersBySlug, 'partnersJSON') ?>
<?= $helper->jsonInScriptTag($objectManagerData, 'objectManagerDataJSON') ?>

<!---->
<div class="delivery-page__info clearfix">
    <div class="delivery-region">
        <span class="delivery-region__msg">Ваш регион</span>
        <span class="delivery-region__current"><?= \App::user()->getRegion()->getName() ?></span>
        <a href="#" class="delivery-region__change-lnk"><span class="delivery-region__change-inn jsChangeRegion">Изменить</span></a>
    </div>
    <div class="deliv-ctrls">
    <!-- Поиск такой же как в одноклике -->
        <div class="deliv-search">
            <div class="deliv-search__input-wrap">
                <input class="deliv-search-input" type="text" placeholder="Искать по улице, метро"/>
                <div class="deliv-search__clear">×</div>
            </div>
            <!-- Саджест такой же как в одноклике -->
            <div class="deliv-suggest" style="display:none">
                <ul class="deliv-suggest__list">
                    <li class="deliv-suggest__i"></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="delivery-map-wrap">

    <!-- Чтобы фон был без градиента - удаляем класс gradient-->
    <ul class="delivery-logo-lst gradient">
        <? foreach ($partners as $partner) : ?>
            <? if (!isset($partner['slug'])) continue ?>
            <!-- Для активности добавить класс active-->
            <li class="delivery-logo-lst-i jsPartnerListItem" data-value="<?= $partner['slug'] ?>">
                <img class="delivery-logo-lst-i__img" src="/styles/delivery/img/<?= $partner['slug'] ?>.png">
                <!-- картинка для плоского фона - другая: <img class="delivery-logo-lst-i__img" src="/styles/delivery/img/logo1-plain.png"> -->
                <div class="delivery-logo-lst-i__close">×</div>
            </li>
        <? endforeach ?>
    </ul>

    <div class="delivery-map">
        <ul class="points-lst deliv-list jsPointList">
            <? foreach ($points as $point) : ?>
            <li class="points-lst-i jsPointListItem" id="uid-<?= $point->uid ?>" data-partner="<?= $point->partner ?>">
                <div class=""><?= $partnersBySlug[$point->partner]['name'] ?></div>
                <div class="deliv-item__addr">
                    <? if ($point->subway) : ?>
                    <div class="deliv-item__metro" style="background: <?= $point->subway->getLine()->getColor() ?>">
                       <div class="deliv-item__metro-inn"><?= $point->subway->getName() ?></div>
                    </div>
                    <? endif ?>
                    <div class="deliv-item__addr-name"><?= $point->address ?></div>
<!--                    <div class="deliv-item__time">--><?//= $point->workingTime ?><!--</div>-->
                    <!-- Ссылка Подробнее -->
                </div>
            </li>
            <? endforeach ?>
        </ul>
        <div id="jsDeliveryMap" style="width: 685px; height: 600px"></div>
    </div>
    <div class="delivery-text">
        <h2>Получение заказа в обычном магазине Enter</h2>
        <p>Вы можете получить свой заказ самовывозом в одном из наших магазинов. Некоторые товары, например товары из категории "Мебель", можно заказать только с доставкой на дом. При оформлении заказа и его подтверждении мы уточним возможность самовывоза.</p>
        <p>Время доставки товара для самовывоза зависит от региона и составляет <b>1-4 дня</b>. При поступлении товара в магазин вы получите смс о доставке заказа. После этого товар резервируется на 2 дня - именно столько времени у вас будет, чтобы приехать за долгожданной покупкой.</p>
        <p>Выбрать ближайший к вам магазин, узнать его график работы и посмотреть схему проезда вы можете <a href="/shops">здесь</a>.</p>

        <h2>Получение заказа в пункте выдачи Enter</h2>
        <p>Специально для тех, кто ценит свое время, мы открываем магазины в удобном формате - пункт выдачи. Пришел – назвал номер заказа – забрал. Без очередей и лишней траты времени. Выдача товаров осуществляется ежедневно по графику работы магазинов.</p>
        <p>В случае если вы хотите получить крупногабаритный заказ, сэкономив на стоимости услуги доставки, в Орле, Туле, Краснодаре, Казани, Белгороде, Брянске, Воронеже, Смоленске, Ярославле открыты такие пункты выдачи.</p>
        <p>Посмотреть, где находится ближайший к вам пункт выдачи, вы можете на <a href="/shops">нашем сайте</a> прямо сейчас.</p>

        <h2>Получение заказа в постамате</h2>
        <p>Любой товар, кроме мебели и легко бьющихся в транспортировке товаров (стекло, керамика и т.п.), вы можете забрать <b>ежедневно с 9.00 до 22.00</b> в постаматах PickPoint, они находятся в местах высокого трафика: торговых центрах, магазинах и т.д. Эта услуга доступна только физическим лицам. Объем упаковки товара должен быть <b>не более 15 л</b>, габариты не должны превышать <b>58 х 58 х 68 см</b>, а вес - быть <b>не больше 50 кг</b>. При получении в постамате покупку можно оплатить банковской картой либо наличными, во втором случае сдача не выдается, но на остаток средств можно, например, пополнить счет мобильного телефона. <b>Срок хранения заказа в постамате 3 дня</b>, по вашему запросу PickPoint может продлить срок хранения еще на 3 дня. Узнать срок доставки в постамат в вашем регионе, а также стоимость данной услуги вы можете, выбрав регион из списка ниже.</p>
    </div>
</div>
