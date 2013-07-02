<?php

return function (
    \Model\Product\BasicEntity $product,
    \Helper\TemplateHelper $helper
) { ?>

<div class="bWidgetService mWidget">
    <div class="bWidgetService__eHead">
        <strong>F1 сервис</strong>
        Установка и настройка
    </div>

    <ul class="bWidgetService__eInputList">
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
</div><!--/widget services -->

<? };