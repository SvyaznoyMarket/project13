<?php
/**
 * @var $page   \View\Layout
 * @var $region \Model\Region\Entity
 * @var $shop   \Model\Shop\Entity
 */
?>

<a class="bMenuBack mBlackLink" href="<?= $page->url('shop') ?>">Наши магазины</a>
<div class="bStoreItem clearfix">
    <div id="map" class="bStoreItem_eMap" data-latitude="<?= $shop->getLatitude() ?>" data-longitude="<?= $shop->getLongitude() ?>"></div>
    <p class="bStoreItem_eAddress"><?= $shop->getAddress() ?></p>
    <p class="bStoreItem_eRegtime">Работаем <?= $shop->getRegime() ?></p>
    <a class="orangeButton mFl" href="tel:<?= preg_replace('/[^\d]/', '', $shop->getPhone()) ?>"><?= $shop->getPhone() ?></a>
</div>