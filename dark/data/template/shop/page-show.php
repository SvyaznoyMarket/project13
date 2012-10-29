<?php
/**
 * @var $page          \View\Shop\ShowPage
 * @var $currentRegion \Model\Region\Entity
 * @var $shop          \Model\Shop\Entity
 */
?>

<input type="hidden" name="shop[id]" value="<?= $shop->getId() ?>"/>
<input type="hidden" name="shop[name]" value="<?= $shop->getName() ?>"/>
<input type="hidden" name="shop[latitude]" value="<?= $shop->getLatitude() ?>"/>
<input type="hidden" name="shop[longitude]" value="<?= $shop->getLongitude() ?>"/>

<!-- bMap -->
<div class="bMap">
    <div id="map-container" class='bMap__eBody'></div>

    <div class='bMap__eImages'>
        <h3 class='bMap__eImagesTitle'>Смотри как внутри</h3>

        <div class='bMap__eScrollWrap'>

            <? $i = 0; $count = count($shop->getPhoto()); foreach ($shop->getPhoto() as $photo): $i++ ?>
            <div class="bMap__eContainer<? if (1 == $i) echo ' first' ?> map-image-link">
                <img class="bMap__eImgSmall" src="<?= $photo->getUrl(5) ?>"/>

                <div class='bMap__eImgBig'>
                    <img src="<?= $photo->getUrl(5) ?>" />
                </div>

                <? if ((1 == $i) && $shop->getPanorama()): ?>
                    <div class="bMap__e360 map-360-link"></div>
                <? endif ?>
            </div>
            <? endforeach ?>

            <div class="bMap__eContainer map-google-link">
                <img class='bMap__eImgSmall' src='/images/map_ico.png'/>

                <div class='bMap__eImgBig'></div>
            </div>

        </div>
    </div>

    <input id="map-panorama" type="hidden" data-swf="<?= $shop->getPanorama()->getSwf() ?>" data-xml="<?= $shop->getPanorama()->getXml() ?>"/>
</div>
<!-- /bMap -->

<!-- bMapInfo -->
<div class='bMapInfo'>
    <div class='bMapInfo__eIco mShop'></div>
    <div class='bMapInfo__eText'>
        <h2 class='bMapInfo__eTitle'><?= $shop->getAddress() ?></h2>

        <p class='bMapInfo__eP'>
            <span class='bMapInfo__eSpan mBig'>

            <? if ($shop->getIsReconstructed()): ?>
                <span class="red">На реконструкции</span><br/>

            <? elseif ($shop->getRegime()): ?>
                Работаем <?= $shop->getRegime() ?><br/>

            <? endif ?>

            <? if ($shop->getPhone()): ?>
                Телефон<?= false !== strpos($shop->getPhone(), ',') ? 'ы' : '' ?>
                : <?= $shop->getPhone() ?><br/>
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

<!-- bMapInfo -->
<div class='bMapInfo'>
    <div class='bMapInfo__eIco mCar'></div>
    <div class='bMapInfo__eText'>
        <h2 class='bMapInfo__eTitle'>Как добраться</h2>

        <? if ($shop->getWayWalk()): ?>
            <p class='bMapInfo__eP'>
            <span class='bMapInfo__eSpan mSmall'>
                <b>Проезд на городском транспорте</b><br>
                <?= $shop->getWayWalk() ?>
            </span>
            </p>
        <? endif ?>

        <? if ($shop->getWayAuto()): ?>
            <p class='bMapInfo__eP'>
            <span class='bMapInfo__eSpan mSmall'>
                <b>Проезд на авто</b><br>
                <?= $shop->getWayAuto() ?>
            </span>
        </p>
        <? endif ?>

    </div>
</div>
<!-- /bMapInfo -->

<div class="clear"></div>

