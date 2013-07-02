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
        <strong>F1 сервис</strong>
        Установка и настройка
    </div>

    <ul class="bWidgetService__eInputList">
<<<<<<< HEAD
        <li>
            <input id="id1" name="name4" type="checkbox" hidden />
            <label class="bCustomInput" for="id1">
                <div class="bCustomInput__eText">
                    <span class="dotted">Подключение электричества</span>

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
    </ul>
    <div class="bWidgetService__eAll"><span class="dotted">Ещё 87 услуг</span><br/>доступны в магазине</div>
=======
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
>>>>>>> 958b93985d8e751e20c1f798432667f15b7bdff3
</div><!--/widget services -->

<? };