<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) {

    $mustache = \App::mustache();

?>

    <!-- Листинг товаров -->
    <ul class="bListing clearfix">
    <!-- Элемент листинга/продукт -->
    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <!-- Блок с именем продукта и иконками доп. просмотра товара -->
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>
            <!-- /Блок с именем продукта и иконками доп. просмотра товара -->

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
                <span class="bAvailible">Только в магазинах</span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
                <span class="bOptions"><span class="bDecor">Варианты</span></span>
            </div>

            <div class="bOptionsSection">
                <i class="bCorner"></i>
                <i class="bCornerDark"></i>

                <ul class="bOptionsList">
                    <li class="bOptionsList__eItem">Цвет обивки</li>
                    <li class="bOptionsList__eItem">Наличие подъемного механизма</li>
                </ul>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>
    <!-- /Элемент листинга/продукт -->

    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор</a></p>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs09.enter.ru/1/1/500/75/167449.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
                <span class="bAvailible">Только в магазинах</span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик)</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
                <span class="bAvailible">Только в магазинах</span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
                <span class="bOptions"><span class="bDecor">Варианты</span></span>
            </div>

            <div class="bOptionsSection">
                <i class="bCorner"></i>
                <i class="bCornerDark"></i>

                <ul class="bOptionsList">
                    <li class="bOptionsList__eItem">Цвет обивки</li>
                    <li class="bOptionsList__eItem">Наличие подъемного механизма</li>
                </ul>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem mLast">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs09.enter.ru/1/1/500/75/167449.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs09.enter.ru/1/1/500/75/167449.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
                <span class="bAvailible">Только в магазинах</span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
                <span class="bOptions"><span class="bDecor">Варианты</span></span>
            </div>

            <div class="bOptionsSection">
                <i class="bCorner"></i>
                <i class="bCornerDark"></i>

                <ul class="bOptionsList">
                    <li class="bOptionsList__eItem">Цвет обивки</li>
                    <li class="bOptionsList__eItem">Наличие подъемного механизма</li>
                </ul>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор</a></p>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bAvailible">Только в магазинах</span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
                <span class="bOptions"><span class="bDecor">Варианты</span></span>
            </div>

            <div class="bOptionsSection">
                <i class="bCorner"></i>
                <i class="bCornerDark"></i>

                <ul class="bOptionsList">
                    <li class="bOptionsList__eItem">Цвет обивки</li>
                    <li class="bOptionsList__eItem">Наличие подъемного механизма</li>
                </ul>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem mLast">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>

    <li class="bListingItem">
        <div class="bListingItem__eInner">
            <div class="bSimplyDesc">
                <p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм</a></p>
                <ul class="bSimplyDescStikers">
                    <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                </ul>
            </div>

            <a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

            <div class="bPriceLine clearfix">
                <span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
                <span class="bAvailible">Только в магазинах</span>
            </div>

            <div class="bPriceLine clearfix">
                <span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
                <span class="bOptions"><span class="bDecor">Варианты</span></span>
            </div>

            <div class="bOptionsSection">
                <i class="bCorner"></i>
                <i class="bCornerDark"></i>

                <ul class="bOptionsList">
                    <li class="bOptionsList__eItem">Цвет обивки</li>
                    <li class="bOptionsList__eItem">Наличие подъемного механизма</li>
                </ul>
            </div>

            <div class="bBtnLine clearfix">
                <div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
                <a class="btnView mBtnGrey" href="">Посмотреть</a>
            </div>
        </div>
    </li>
    </ul>
    <!-- /Листинг товаров -->

<? };