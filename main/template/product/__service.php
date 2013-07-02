<?php

return function (
    \Model\Product\Entity $product,
    \Helper\TemplateHelper $helper
) { ?>

<div class="bWidgetService mWidget">
    <div class="bWidgetService__eHead">
        <strong>F1 сервис</strong>
        Установка и настройка
    </div>

    <ul class="bWidgetService__eInputList">
    <? foreach ($product->getService() as $service): ?>
        <li>
            <input class="<?= \View\Id::cartButtonForProductService($product->getId(), $service->getId()) ?>" type="checkbox" hidden />
            <label class="bCustomInput" for="id1">
                <div class="bCustomInput__eText">
                    <span class="dotted"><?= $service->getName() ?></span>

                    <? if ($service->getDescription()): ?>
                        <?= $helper->render('__hint', ['name' => $service->getName(), 'value' => $service->getDescription()]) ?>
                    <? endif ?>

                    <div class="bCustomInput__ePrice"><strong><?= $helper->formatPrice($service->getPrice()) ?></strong> <span class="rubl">p</span></div>
                </div>
            </label>
        </li>
    <? endforeach ?>
    </ul>
    <!--<div class="bWidgetService__eAll"><span class="dotted">Ещё 87 услуг</span><br/>доступны в магазине</div>-->
</div><!--/widget services -->

<? };