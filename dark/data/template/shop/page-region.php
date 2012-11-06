<?php
/**
 * @var $page          \View\Shop\ShowPage
 * @var $currentRegion \Model\Region\Entity
 * @var $shops         \Model\Shop\Entity[]
 * @var $markers       array
 */
?>

<? if ((bool)$shops): ?>
    <? require __DIR__ . '/_map.php' ?>

    <!-- bMapInfo -->
    <div class='bMapShops__eInfo'>
        <h2 class='bMapShops__eTitle'>Все магазины Enter в <?= $currentRegion->getInflectedName(5) ?></h2>

        <? foreach ($shops as $shop): ?>
            <!-- __eCard -->
            <div class='bShopCard' onclick="window.location='<?= $page->url('shop.show', array('regionToken' => $currentRegion->getToken(), 'shopToken' => $shop->getToken())) ?>'">
                <img class='bShopCard__eIco' src='<?= $shop->getImageUrl(2) ?>' width="162" height="100" />
                <h3 class='bShopCard__eTitle'><?= $shop->getName() ?></h3>
                <? if ($shop->getIsReconstructed()): ?>
                    <p class='bShopCard__eDescription red'>На реконструкции</p>
                <? elseif ($shop->getRegime()) :?>
                    <p class='bShopCard__eDescription'>Работаем <?= $shop->getRegime() ?></p>
                <? endif ?>

                <a href="<?= $page->url('shop.show', array('regionToken' => $currentRegion->getToken(), 'shopToken' => $shop->getToken())) ?>" class="bShopCard__eView">Подробнее о магазине</a>
            </div>
            <!-- /__eCard -->
        <? endforeach ?>

    </div>
    <!-- /bMapInfo -->

    <div class="clear"></div>
    <br />

<? else: ?>
    <p>В этом городе пока нет магазинов.</p>

<? endif ?>