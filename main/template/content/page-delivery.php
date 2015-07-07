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
        <a href="#" class="delivery-region__change-lnk"><span class="delivery-region__change-inn jsChangeRegion">Изменить регион</span></a>
    </div>
    <div class="deliv-ctrls">
    <!-- Поиск такой же как в одноклике -->
        <div class="deliv-search">
            <div class="deliv-search__input-wrap">
                <input id="searchInput" class="deliv-search-input" type="text" placeholder="Искать по улице, метро"/>
                <div class="deliv-search__clear jsSearchClear" style="display: none">×</div>
            </div>
            <!-- Саджест такой же как в одноклике -->
            <div class="deliv-suggest jsSearchAutocompleteHolder" style="display:none; top: 33px;">
                <ul class="deliv-suggest__list jsSearchAutocompleteList">
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
                <div class="delivery-logo-lst-i__close"></div>
            </li>
        <? endforeach ?>
    </ul>

    <div class="delivery-map">
        <ul class="points-lst deliv-list jsPointList">
            <? foreach ($points as $point) : ?>

            <li class="points-lst-i jsPointListItem" id="uid-<?= $point->uid ?>" data-geo="<?= $helper->json([$point->latitude, $point->longitude]) ?>" data-partner="<?= $point->partner ?>">
                <div class="points-lst-i__partner jsPointListItemPartner"><?= $point->getPartnerName($partnersBySlug) ?></div>

                <div class="deliv-item__addr">
                    <? if ($point->subway) : ?>
                    <div class="deliv-item__metro" style="background: <?= $point->subway->getLine()->getColor() ?>">
                       <div class="deliv-item__metro-inn"><?= $point->subway->getName() ?></div>
                    </div>
                    <? endif ?>
                    <div class="deliv-item__addr-name"><?= $point->address ?></div>
<!--                    <div class="deliv-item__time">--><?//= $point->workingTime ?><!--</div>-->


                </div>
                <!-- Ссылка Подробнее -->
                <!--a href="" class="points-lst-i__more">Подробнее</a-->
            </li>
            <? endforeach ?>
        </ul>
        <div class="map-container" id="jsDeliveryMap"></div>
    </div>
    <div class="delivery-text">
        <h2>Бесплатный самовывоз</h2>
        <ul class="delivery-list">
            <li><b>Из любого магазина Enter</b></li>
            <li>Из магазина «Связной» на всей территории присутствия</li>
            <li>При заказе на сумму от 1990&thinsp;<span class="rubl">p</span>&nbsp;в пунктах выдачи заказа PickPoint, Hermes-DPD</li>
        </ul>

        <p>Бесплатная доставка заказов в пункты выдачи PickPoint и Hermes-DPD осуществляется в городах:</p>

        <table class="delivery-city-table">
            <tbody><tr>
                <td style="width: 170px;">Москва</td>
                <td style="width: 170px;">Казань</td>
                <td style="width: 150px;">Рязань</td>
            </tr>
            <tr>
                <td>Санкт-Петербург</td>
                <td>Калуга</td>
                <td>Саратов</td>
            </tr>
            <tr>
                <td>Белгород</td>
                <td>Краснодар</td>
                <td>Смоленск</td>
            </tr>
            <tr>
                <td>Брянск</td>
                <td>Курск</td>
                <td>Ставрополь</td>
            </tr>
            <tr>
                <td>Владимир</td>
                <td>Липецк</td>
                <td>Тамбов</td>
            </tr>
            <tr>
                <td>Волгоград</td>
                <td>Нижний Новгород</td>
                <td>Тверь</td>
            </tr>
            <tr>
                <td>Воронеж</td>
                <td>Орел</td>
                <td>Тула</td>
            </tr>
            <tr>
                <td>Иваново</td>
                <td>Ростов-на-Дону</td>
                <td>Ярославль</td>
            </tr>
            </tbody></table>
        <h2 >Получение заказа</h2><table class="delivery-table">
            <tbody style="vertical-align: top;">
            <tr>
                <th style="text-align: center;border-bottom: 1px solid #FFF;   border-left: 1px solid #FFF;   border-top: 1px solid #FFF;"></th>
                <th>Резерв товара</th>
                <th>Доступные способы оплаты</th>
                <th><span class="measures">Ограничения по&nbsp;габаритам, весу, объему</span></th>
                <th>Ограниячения по&nbsp;категориям товара</th>
                <th>Дополнительная информация</th>
            </tr>
            <tr>
                <td style="text-align: center;border-bottom: 1px solid #FFF;border-left: 1px solid #FFF;border-top: 1px solid #FFF;"><img class="table-img" src="/styles/delivery/img/Enter-s.png">
                    <span class="table-lbl">Магазины и&nbsp;пункты выдачи Enter</span>
                </td>
                <td style="vertical-align: top;">3 дня</td>
                <td>банковская карта,<br>наличные,<br>Бонусы «Спасибо&nbsp;от&nbsp;Сбербанка»,<br>подарочные сертификаты Enter</td>
                <td>макс. 2 м по&nbsp;любой из&nbsp;сторон упаковки;<br>25 кг; 100 л</td>
                <td>мебель;<br>другие крупногабаритные товары только с&nbsp;доставкой на&nbsp;дом</td>
                <td style="
">в&nbsp;г.&nbsp;Чехове, ул.&nbsp;Чехова, д.&nbsp;3<br>и&nbsp;в&nbsp;г.&nbsp;Ростове-на-Дону, пр-т Коммунистический, д.&nbsp;2 можно&nbsp;получить стулья, офисные&nbsp;кресла, пуфы</td>
            </tr>
            <tr>
                <td style="text-align: center; border-bottom: 1px solid #FFF;   border-left: 1px solid #FFF;   border-top: 1px solid #FFF;"><img class="table-img" src="/styles/delivery/img/PickPoint2-s.png">
                    <span class="table-lbl">Постаматы PickPoint</span></td>
                <td>3 дня (+3&nbsp;дня по вашему запросу)</td>
                <td>банковская карта,<br>наличные<br>(при оплате наличными сдача не&nbsp;выдается, есть&nbsp;возможность зачисления, например, на&nbsp;счет мобильного телефона)</td>
                <td>макс. 58х58х68&nbsp;см по&nbsp;любой из&nbsp;сторон измерения;<br>15 кг</td>
                <td> мебель;<br>легко бьющиеся товары (стекло, керамика и&nbsp;т.п.)</td>
                <td><br></td>
            </tr>
            <tr>
                <td style="text-align: center;  border-bottom: 1px solid #FFF;   border-left: 1px solid #FFF;   border-top: 1px solid #FFF;"><img class="table-img" src="/styles/delivery/img/PickPoint-s.png">
                    <span class="table-lbl">Пункты выдачи PickPoint</span></td>
                <td>3 дня (+3&nbsp;дня по вашему запросу)</td>
                <td>наличные</td>
                <td>макс. 58х58х68&nbsp;см по&nbsp;любой из&nbsp;сторон измерения;<br>15 кг</td>
                <td> мебель;<br>легко бьющиеся товары (стекло, керамика и&nbsp;т.п.)</td>
                <td><br></td>
            </tr>
            <tr>
                <td style="text-align: center;border-bottom: 1px solid #FFF;   border-left: 1px solid #FFF;   border-top: 1px solid #FFF;"><img class="table-img" src="/styles/delivery/img/svz.png">
                    <span class="table-lbl">Магазины «Связной»</span></td>
                <td>7 дней</td>
                <td>наличные<br>(предоплата&nbsp;на&nbsp;сайте невозможна)</td>
                <td>макс. 120х120х120 см;<br>25 кг; 150 л</td>
                <td>мебель;<br>ювелирные украшения;<br> парфюмерия и&nbsp;косметика;<br>легко бьющиеся товары</td>
                <td>выдача товаров в&nbsp;20&nbsp;городах:<br> Архангельск, Воронеж, Екатеринбург, Ижевск, Москва, Мурманск, Набережные&nbsp;Челны, Нижний&nbsp;Новгород, Пермь, Самара, Санкт-Петербург, Саратов, Тверь, Тольятти, Тула, Тюмень, Ульяновск, Уфа, Челябинск, Ярославль<br></td>
            </tr>
            <tr>
                <td style="text-align: center;  border-bottom: 1px solid #FFF;   border-left: 1px solid #FFF;   border-top: 1px solid #FFF;"><img class="table-img" src="/styles/delivery/img/Hermes-s.png">
                    <span class="table-lbl">Пункты выдачи Hermes-DPD</span></td>
                <td>5 дней</td>
                <td>наличные</td>
                <td>макс. 58х58х68&nbsp;см;<br>30 кг</td>
                <td>мебель;<br>ювелирные украшения;<br> легко бьющиеся товары (стекло, керамика и&nbsp;т.п.)</td>
                <td><br></td>
            </tr>
            <tr>
                <td style="text-align: center;  border-bottom: 1px solid #FFF;   border-left: 1px solid #FFF;   border-top: 1px solid #FFF;"><img class="table-img" src="/styles/delivery/img/euroset-s.png">
                    <span class="table-lbl">Магазины Евросеть</span></td>
                <td>7 дней</td>
                <td>банковская карта (возможна предоплата при оформлении заказа на сайте), наличные</td>
                <td>макс. 58х58х68&nbsp;см;<br>4 кг; 40 л</td>
                <td>мебель;<br>ювелирные украшения;<br> легко бьющиеся товары (стекло, керамика и&nbsp;т.п.)</td>
                <td><br></td>
            </tr>
            </tbody>
        </table>
    </div>
    <h2>Как работают постаматы PickPoint</h2>
    <div class="delivery-video">
        <video width="100%" controls="controls" poster="http://content.enter.ru/wp-content/uploads/2015/07/685x385.jpg">
            <source src="http://content.enter.ru/wp-content/uploads/2015/07/PickPoint.mp4" type="video/mp4" />
            <source src="http://content.enter.ru/wp-content/uploads/2015/07/PickPoint.webm" type='video/webm; codecs="vp8, vorbis"' />
        </video>
    </div>
    <br>
    <br>
</div>