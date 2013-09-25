<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (!(bool)$product->getService()) {
        return '';
    }
?>

<div class="bWidgetService mWidget">
    <div class="bWidgetService__eHead">
        <strong>F1 сервис</strong>
        Установка и настройка
    </div>

    <ul class="bInputList">
    <? foreach ($product->getService() as $service): ?>
    <?
        $id = \View\Id::cartButtonForProductService($product->getId(), $service->getId());
    ?>
        <li class="bInputList__eListItem">
            <input
                id="<?= $id ?>"
                class="<?= $id ?> bCustomInput mCustomCheckbox"
                name="<?= $product->getId()?>"
                type="checkbox"
                hidden
                data-set-url="<?= $helper->url('cart.service.set', ['serviceId' => $service->getId(), 'productId' => $product->getId()]) ?>"
                data-delete-url="<?= $helper->url('cart.service.delete', ['serviceId' => $service->getId(), 'productId' => $product->getId()]) ?>"
            />

            <label class="bCustomLabel" for="<?= $id ?>">
                <span class="dotted"><?= $service->getName() ?></span>

                <? if ($service->getDescription()): ?>
                    <?= $helper->render('__hint', ['name' => $service->getName(), 'value' => $service->getDescription()]) ?>
                <? endif ?>

                <div class="bCustomInput__ePrice"><strong><?= $helper->formatPrice($service->getPrice()) ?></strong> <span class="rubl">p</span></div>
            </label>
        </li>
    <? endforeach ?>
    </ul>
    <!--<div class="bWidgetService__eAll"><span class="dotted">Ещё 87 услуг</span><br/>доступны в магазине</div>-->
</div><!--/widget services -->

<? };