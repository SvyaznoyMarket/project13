<?php

$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Region\Entity $region
) { ?>

<noindex>
    <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json(['latitude' => $region->getLatitude(), 'longitude' => $region->getLongitude(), 'zoom' => 10])?>"></div>
    <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ) ?>"></div>
    <div id="region-name" data-value=<?= $helper->json($region->getName()) ?>></div>

    <div id="jsOneClickContent" class="popup popup-w635">
        <a class="close" href="#">Закрыть</a>

        <div id="jsOneClickContentPage">
            <?= $helper->render('order-v3-1click/__form', [
                'product' => $product,
            ]) ?>
        </div>
    </div>
</noindex>

<? }; return $f;