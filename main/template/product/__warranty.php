<?php

return function (
<<<<<<< HEAD
    \Model\Product\BasicEntity $product,
=======
    \Model\Product\Entity $product,
>>>>>>> 958b93985d8e751e20c1f798432667f15b7bdff3
    \Helper\TemplateHelper $helper
) { ?>

<div class="bWidgetService mWidget">
    <div class="bWidgetService__eHead">
        <strong>Под защитой F1</strong>
        Расширенная гарантия
    </div>

    <ul class="bWidgetService__eInputList">
<<<<<<< HEAD
        <li>
            <input id="id4" name="name1" type="radio" hidden />
            <label class="bCustomInput" for="id4">
                <div class="bCustomInput__eText">
                    <span class="dotted">Black: 2 годa</span>

                    <div class="bHint">
                        <a class="bHint_eLink">Разрешение дисплея</a>
                        <div class="bHint_ePopup popup">
                            <div class="close"></div>
                            <div class="bHint-text">
                                <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bCustomInput__ePrice"><strong>1 490</strong> <span class="rubl">p</span></div>
                </div>
            </label>
        </li>

        <li>
            <input id="id3" name="name1" type="radio" hidden />
            <label class="bCustomInput" for="id3">
                <div class="bCustomInput__eText">
                    <span class="dotted">Gold: 2,5 годa</span>

                    <div class="bHint">
                        <a class="bHint_eLink">Разрешение дисплея</a>
                        <div class="bHint_ePopup popup">
                            <div class="close"></div>
                            <div class="bHint-text">
                                <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bCustomInput__ePrice"><strong>1 490</strong> <span class="rubl">p</span></div>
                </div>
            </label>
            <div style="display: none;" class="bDeSelect"><a href="">Отменить</a></div>
        </li>

        <li>
            <input id="id2" name="name1" type="radio" hidden />
            <label class="bCustomInput" for="id2">
                <div class="bCustomInput__eText">
                    <span class="dotted">Platinum: 3 годa</span>

                    <div class="bHint">
                        <a class="bHint_eLink">Разрешение дисплея</a>
                        <div class="bHint_ePopup popup">
                            <div class="close"></div>
                            <div class="bHint-text">
                                <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bCustomInput__ePrice"><strong>1 490</strong> <span class="rubl">p</span></div>
=======
    <? foreach ($product->getWarranty() as $warranty): ?>
        <li>
            <input class="<?= \View\Id::cartButtonForProductWarranty($product->getId(), $warranty->getId()) ?>" type="radio" hidden />
            <label class="bCustomInput" for="id2">
                <div class="bCustomInput__eText">
                    <span class="dotted"><?= $warranty->getName() ?></span> <?= $warranty->getPeriod() . '&nbsp;' . $helper->numberChoice($warranty->getPeriod(), ['месяц', 'месяца', 'месяцев']) ?>

                    <? if ($warranty->getDescription()): ?>
                        <?= $helper->render('__hint', ['name' => $warranty->getName(), 'value' => $warranty->getDescription()]) ?>
                    <? endif ?>

                    <div class="bCustomInput__ePrice"><strong><?= $helper->formatPrice($warranty->getPrice()) ?></strong> <span class="rubl">p</span></div>
>>>>>>> 958b93985d8e751e20c1f798432667f15b7bdff3
                </div>
            </label>
            <div style="display: none;" class="bDeSelect"><a href="">Отменить</a></div>
        </li>
<<<<<<< HEAD
=======
    <? endforeach ?>
>>>>>>> 958b93985d8e751e20c1f798432667f15b7bdff3
    </ul>
</div><!--/widget services -->

<? };