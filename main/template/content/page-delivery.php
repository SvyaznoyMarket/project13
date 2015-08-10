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

<?= $helper->jsonInScriptTag($partnersBySlug, 'partnersJSON') ?>
<?= $helper->jsonInScriptTag($objectManagerData, 'objectManagerDataJSON') ?>

<!---->
<div class="delivery-page__info clearfix">
    <div class="delivery-region">
        <span class="delivery-region__msg">Ваш регион</span>
        <a class="delivery-region__current jsChangeRegion"><span class="delivery-region__current-inn"><?= \App::user()->getRegion()->getName() ?></span></a>
        <a href="#deliv-free" class="delivery-free__lnk"><span class="delivery-free__inn">Бесплатный самовывоз</span></a>
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
                </div>
            </li>
            <? endforeach ?>
        </ul>
        <div class="map-container" id="jsDeliveryMap"></div>
    </div>
    <?= $content ?>
</div>