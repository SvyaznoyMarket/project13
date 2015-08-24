<?
/**
 * @var $points Model\Point\ScmsPoint[]
 * @var $partners []
 * @var $partnersBySlug []
 * @var $objectManagerData []
 * @var $content string
 * @var $page View\Content\DeliveryMapPage
 */
$helper = \App::helper();
?>

<?= $helper->jsonInScriptTag($points, 'points') ?>

<div class="js-module-require" data-module="deliveryPage">
    <div class="delivery-info-head">
        <!-- Поиск такой же как в одноклике -->
        <div class="point-search">
            <i class="point-search__icon i-controls i-controls--search"></i>
            <input class="point-search__it it js-pointpopup-search" type="text" placeholder="Искать по улице, метро">
            <div class="point-search" style="display: none;">×</div>

            <div class="pick-point-suggest js-pointpopup-autocomplete" style="display: none">
                <ul class="pick-point-suggest__list js-pointpopup-autocomplete-wrapper"></ul>
            </div>
        </div>

        <div class="delivery-region">
            <span class="delivery-region__msg">Ваш регион</span>
            <a class="delivery-region__current js-popup-show js-change-region" data-popup="region"><span class="delivery-region__current-inn"><?= \App::user()->getRegion()->getName() ?></span></a>
            <a href="#deliv-free" class="delivery-free dotted">Бесплатный самовывоз</a>
        </div>
    </div>

    <div class="delivery-map-wrap">
        <ul class="delivery-logo-lst">
            <? foreach ($partners as $partner) : ?>
                <? if (!isset($partner['slug'])) continue ?>
                <!-- Для активности добавить класс active-->
                <li class="delivery-logo-lst__i jsPartnerListItem" data-value="<?= $partner['slug'] ?>">
                    <img class="delivery-logo-lst__img" src="/styles/delivery/img/<?= $partner['slug'] ?>.png">
                    <!-- картинка для плоского фона - другая: <img class="delivery-logo-lst-i__img" src="/styles/delivery/img/logo1-plain.png"> -->
                    <div class="delivery-logo-lst__close icon-clear"></div>
                </li>
            <? endforeach ?>
        </ul>

        <div class="delivery-map">
            <ul class="points-lst deliv-list js-pointpopup-points-wrapper"></ul>
            <div class="map-container" id="jsDeliveryMap"></div>
        </div>
        <?= $content ?>
    </div>

    <script id="js-point-template" type="text/template" class="hidden">
    {{#point}}
        {{#shown}}
            <li class="points-lst-i js-pointpopup-pick-point">
                <div class="points-lst-i__partner jsPointListItemPartner">{{partner}}</div>

                <div class="deliv-item__addr">
                    {{#subway}}
                        <div class="deliv-item__metro" style="background: {{color}}">
                            <div class="deliv-item__metro-inn">{{name}}</div>
                        </div>
                    {{/subway}}
                    <div class="deliv-item__addr-name">{{address}}</div>
                </div>
            </li>
        {{/shown}}
    {{/point}}
    </script>

    <script id="js-pointpopup-autocomplete-template" type="text/template" class="hidden">
        {{#bounds}}
            <li class="js-pointpopup-autocomplete-item" data-bounds="{{bounds}}">{{name}}</li>
        {{/bounds}}
    </script>
</div>