<?php

return function (
    \Model\Product\BasicEntity $product,
    \Helper\TemplateHelper $helper
) { ?>

<div class="bWidgetService mWidget">
    <div class="bWidgetService__eHead">
        <strong>Под защитой F1</strong>
        Расширенная гарантия
    </div>

    <ul class="bWidgetService__eInputList">
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
                </div>
            </label>
            <div style="display: none;" class="bDeSelect"><a href="">Отменить</a></div>
        </li>
    </ul>
</div><!--/widget services -->

<? };