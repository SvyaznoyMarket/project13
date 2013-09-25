<?php
/**
 * @var $page    \View\Layout
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 */
?>

<?
$defaultId = \App::config()->region['defaultId'];
$nearestCities = $product->getNearestCity();
$defaultCity = null;
foreach ($nearestCities as $i => $city) {
    if ($city->getId() == $defaultId) {
        $defaultCity = $city;
        // удаляем город по умолчанию из списка ближайших городов
        unset($nearestCities[$i]);
        break;
    }
}
?>

<div class="otherRegion">
    <div class="corner">
        <div></div>
    </div>
    <p>Этот товар нельзя купить в <?= $user->getRegion()->getInflectedName(5) ?></p>
    <p>
        Товар доступен
        <? if ($defaultCity): // если город по умолчанию есть в списке ближайших городов ?>
        в <strong><a href="<?= $page->url('region.change', array('regionId' => $defaultCity->getId())) ?>"><?= $defaultCity->getInflectedName(5) ?></a></strong>
        <? endif ?>

        <? if ((bool)$nearestCities): ?>
            <? if ($defaultCity): ?>и <? endif ?>
            <? if (1 == count($nearestCities)): ?>
                <?
                /** @var $city \Model\Region\Entity */
                $city = reset($nearestCities);
                ?>
                в <a href="<?= $page->url('region.change', array('regionId' => $city->getId())) ?>"><?= $city->getInflectedName(5) ?></a>
            <? else: ?>
                в <a class="expander" href="#">других городах:</a>
            <? endif ?>
        <? endif ?>
    </p>

    <? if (count($nearestCities) > 1): ?>
    <ul style="display: none;">
    <? foreach ($nearestCities as $city): ?>
        <li>
            <a href="<?= $page->url('region.change', array('regionId' => $city->getId())) ?>"><?= $city->getInflectedName(5) ?></a>
        </li>
    <? endforeach ?>
    </ul>
    <? endif ?>

    <div class="clear"></div>

</div>

<a class="likeGoodsRegion" href="<?= $product->getMainCategory()->getLink() ?>">Похожие товары, доступные в вашем городе</a>
<div class="line pb15"></div>