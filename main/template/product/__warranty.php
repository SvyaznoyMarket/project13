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

    <ul class="bWidgetService__eInputList">
    <? foreach ($product->getWarranty() as $warranty): ?>
    <?
        $id = \View\Id::cartButtonForProductWarranty($product->getId(), $warranty->getId());
    ?>
        <li>
            <input id="<?= $id ?>" class="<?= $id ?>" name="<?= $product->getId()?>" type="radio" hidden />
            <label class="bCustomInput" for="<?= $id ?>">
                <div class="bCustomInput__eText">
                    <span class="dotted"><?= $warranty->getName() ?></span> <?= $warranty->getPeriod() . '&nbsp;' . $helper->numberChoice($warranty->getPeriod(), ['месяц', 'месяца', 'месяцев']) ?>

                    <? if ($warranty->getDescription()): ?>
                        <?= $helper->render('__hint', ['name' => $warranty->getName(), 'value' => $warranty->getDescription()]) ?>
                    <? endif ?>

                    <div class="bCustomInput__ePrice"><strong><?= $helper->formatPrice($warranty->getPrice()) ?></strong> <span class="rubl">p</span></div>
                </div>
            </label>
            <div style="display: none;" class="bDeSelect"><a href="">Отменить</a></div>
        </li>
    <? endforeach ?>
    </ul>
</div><!--/widget services -->

<? };