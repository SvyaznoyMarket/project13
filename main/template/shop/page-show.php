<?php
/**
 * @var $page          \View\Shop\ShowPage
 * @var $shop          \Model\Shop\Entity
 */
?>

<input type="hidden" name="shop[id]" value="<?= $shop->getId() ?>"/>
<input type="hidden" name="shop[name]" value="<?= $shop->getName() ?>"/>
<input type="hidden" name="shop[latitude]" value="<?= $shop->getLatitude() ?>"/>
<input type="hidden" name="shop[longitude]" value="<?= $shop->getLongitude() ?>"/>

<!-- bMap -->
<img class="printLogo" src='/css/shop/img/bw_logo.png' alt="Enter logo" />
<div class="bMap">
    <div id="map-container" class='bMap__eBody'></div>
    <div class='bMap__eImages'>
        <? if (false): ?>
            <h3 class='bMap__eImagesTitle'>Смотри как внутри</h3>
        <? endif ?>

        <div class='bMap__eScrollWrap'>

            <? $i = 0; $count = count($shop->getPhoto()); foreach ($shop->getPhoto() as $photo): $i++ ?>
            <div class="bMap__eContainer<? if (1 == $i) echo ' first' ?> map-image-link">
                <img class="bMap__eImgSmall" src="<?= (1 == $i) ? $photo->getUrl('shop_middle'): $photo->getUrl('shop_small') ?>" alt="" data-value="<?= $photo->getUrl('shop_big') ?>" />

                <? if ((1 == $i) && $shop->getPanorama()): ?>
                    <div class="bMap__e360 map-360-link"></div>
                <? endif ?>
            </div>
            <? endforeach ?>

            <div class="bMap__eContainer map-google-link">
                <img class='bMap__eImgSmall' src='/images/map_ico.png' alt="" />

                <div class='bMap__eImgBig'></div>
            </div>

        </div>
    </div>

    <? if ($shop->getPanorama()): ?>
        <input id="map-panorama" type="hidden" data-swf="<?= $shop->getPanorama()->getSwf() ?>" data-xml="<?= $shop->getPanorama()->getXml() ?>"/>
    <? endif ?>

    <div id="staticYMap"><img src="" alt="yandex map for printer" /></div>
</div>
<!-- /bMap -->
<a class="bOrangeButton printAction" href="javascript:window.print()">Распечатать</a>
<div class="clear"></div>
<!-- bMapInfo -->
<div class='bMapInfo'>
    <div class='bMapInfo__eIco mShop'></div>
    <div class='bMapInfo__eText'>
        <h2 class='bMapInfo__eTitle mShop'>
            <?= $shop->getAddress() ?>
        </h2>

        <? if ($productCount = $shop->getProductCount()): ?>
            <div class="shopInStock shopInStock-mb"><?= $productCount ?> <?= $page->helper->numberChoice($productCount, ['товар', 'товара', 'товаров'])?> можно забрать сегодня</div>
        <? endif ?>

        <p class='bMapInfo__eP'>
            <span class='bMapInfo__eSpan mBig'>

            <? if ($shop->getIsReconstructed()): ?>
                <span class="red">На реконструкции</span><br/>
            <? elseif ($shop->getRegime()): ?>
                Работаем <?= $shop->getRegime() ?><br/>
            <? endif ?>

            <? if ($shop->getPhone()): ?>
                Телефон<?= false !== strpos($shop->getPhone(), ',') ? 'ы' : '' ?>: <?= $shop->getPhone() ?><br/>
            <? endif ?>
            </span>
        </p>

        <p class='bMapInfo__eP'>
            <span class='bMapInfo__eSpan mSmall'>
                <?= $shop->getDescription() ?>
            </span>
        </p>

    </div>
</div>
<!-- /bMapInfo -->

<? if ($shop->getWayWalk() || $shop->getWayAuto()): ?>
<!-- bMapInfo -->
<div class='bMapInfo'>
    <div class='bMapInfo__eIco mCar'></div>
    <div class='bMapInfo__eText'>
        <h2 class='bMapInfo__eTitle'>Как добраться</h2>

        <? if ($shop->getWayWalk()): ?>
            <p class='bMapInfo__eP'>
                <span class='bMapInfo__eSpan mSmall'>
                    <b>Проезд на городском транспорте</b><br />
                </span>
            </p>
            <?= $shop->getWayWalk() ?>
        <? endif ?>

        <? if ($shop->getWayAuto()): ?>
            <p class='bMapInfo__eP'>
                <span class='bMapInfo__eSpan mSmall'>
                    <b>Проезд на авто</b><br />
                </span>
            </p>
            <?= $shop->getWayAuto() ?>
        <? endif ?>

    </div>
</div>
<? endif ?>

<!-- /bMapInfo -->
<div class="clear"></div>

