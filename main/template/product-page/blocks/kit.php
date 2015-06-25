<?php
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) { ?>

    <!-- набор-пакет -->
    <div class="product-section set-section clearfix" id="kit">
        <div class="product-section__tl">Базовая комплектация набора</div>
        <!--список комплектующих-->
        <ul class="set-section-package">
            <!-- элемент комплекта -->
            <li class="set-section-package-i">
                <a class="set-section-package-i__img" href="/product/furniture/komod-kseno-stl07807-2050403009443"><img src="http://4.imgenter.ru/uploads/media/08/e9/44/thumb_4b3b_product_120.jpeg"></a><!--/ изображение товара -->

                <div class="set-section-package-i__desc rown">
                    <div class="name"><a class="" href="/product/furniture/komod-kseno-stl07807-2050403009443">Комод «Ксено СТЛ.078.07»</a></div><!--/ название товара -->


                    <!-- размеры товара -->
                    <div class="column dimention">
                        <span class="dimention__name">Высота</span>
                        <span class="dimention__val">80.2</span>
                    </div>

                    <div class="column dimention">
                        <span class="dimention__name">&nbsp;</span>
                        <span class="dimention__val separation">x</span>
                    </div>

                    <div class="column dimention">
                        <span class="dimention__name">Ширина</span>
                        <span class="dimention__val">153</span>
                    </div>

                    <div class="column dimention">
                        <span class="dimention__name">&nbsp;</span>
                        <span class="dimention__val separation">x</span>
                    </div>

                    <div class="column dimention">
                        <span class="dimention__name">Глубина</span>
                        <span class="dimention__val">44.5</span>
                    </div>

                    <div class="column dimention">
                        <span class="dimention__name">&nbsp;</span>
                        <span class="dimention__val">см</span>
                    </div>
                    <!--/ размеры товара -->

                </div>

                <div class="set-section-package-i__price rown">
                    7220&nbsp;<span class="rubl">p</span>
                </div><!--/ цена -->

                <div class="set-section-package-i__qnt rown">1 шт.</div><!--/ количество в наборе -->
            </li>
            <!-- элемент комплекта END -->
        </ul>
        <!--список комплектующих END-->
    </div>

<? }; return $f;