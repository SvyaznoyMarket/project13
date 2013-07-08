<?php

return function (
    \Model\Product\Entity $product,
    \Helper\TemplateHelper $helper
) { ?>

<div class="bWidgetService mWidget">
    <div class="bWidgetService__eHead">
        <strong>Под защитой F1</strong>
        Расширенная гарантия
    </div>

    <ul class="bInputList mWarranty">
    <? foreach ($product->getWarranty() as $warranty): ?>
    <?
        $id = \View\Id::cartButtonForProductWarranty($product->getId(), $warranty->getId());
    ?>
        <li class="bInputList__eListItem">
            <input
                id="<?= $id ?>"
                class="<?= $id ?> jsCustomRadio bCustomInput mCustomRadio"
                name="<?= $product->getId()?>"
                type="radio"
                hidden
                data-set-url="<?= $helper->url('cart.warranty.set', ['warrantyId' => $warranty->getId(), 'productId' => $product->getId()]) ?>"
                data-delete-url="<?= $helper->url('cart.warranty.delete', ['warrantyId' => $warranty->getId(), 'productId' => $product->getId()]) ?>"
            />

            <label class="bCustomLabel" for="<?= $id ?>">
                <span class="dotted"><?= $warranty->getName() ?></span> <?= $warranty->getPeriod() . '&nbsp;' . $helper->numberChoice($warranty->getPeriod(), ['месяц', 'месяца', 'месяцев']) ?>

                <? if ($warranty->getDescription()): ?>
                    <?= $helper->render('__hint', ['name' => $warranty->getName(), 'value' => $warranty->getDescription()]) ?>
                <? endif ?>

                <div class="bCustomInput__ePrice"><strong><?= $helper->formatPrice($warranty->getPrice()) ?></strong> <span class="rubl">p</span></div>
            </label>
        </li>
    <? endforeach ?>
        <li class="bInputList__eListItem">
            <div style="display: none;" class="bDeSelect" name="<?= $product->getId()?>"><a href="">Отменить</a></div>
        </li>
    </ul>
</div><!--/widget services -->

<? };