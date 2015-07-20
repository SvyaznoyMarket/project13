<?
    // return null;
    use \Model\Product\Filter\Option\Entity as Option;

    /**
     * @var $productFilter  \Model\Product\Filter
     * @var $baseUrl        string
     */

     $helper = \App::helper();

    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;
    /** @var \Model\Product\Filter\Entity $labelFilter */
    $labelFilter = null;
    /** @var \Model\Product\Filter\Entity $widthFilter */
    $brandFilter1 = null;
    /** @var \Model\Product\Filter\Entity $brandFilter2 */
    $brandFilter2 = null;
    /** @var \Model\Product\Filter\Entity[] $tyreFilters */
    $tyreFilters = [];

    $hasSelectedOtherBrands = false;

    $countInListFilters = 0;
    foreach ($productFilter->getUngroupedPropertiesV2() as $key => $property) {
        if (!$property->getIsInList()) {
            continue;
        } else if ($property->isPrice()) {
            $priceFilter = $property;
            $priceFilter->setStepType('price');
        } else if ($property->isLabel()) {
            $labelFilter = $property;
        } else if ($property->isBrand() && $property->getIsAlwaysShow()) {
            $brandFilter1 = clone $property;

            $brandFilter2 = clone $property;
            $brandFilter2->deleteAllOptions();

            if (count($brandFilter1->getOption()) >= 10) {
                $values = $productFilter->getValue($property);
                while (count($brandFilter1->getOption()) >= 9) {
                    $option = $brandFilter1->deleteLastOption();
                    if (in_array($option->getId(), $values)) {
                        $hasSelectedOtherBrands = true;
                    }

                    $brandFilter2->unshiftOption($option);
                }
            }
        } else {
            if ($property->isBrand()) { // Сортировка брендов по алфавиту
                $option = $property->getOption();
                usort($option, function(Option $a, Option $b){ return $a->getName() > $b->getName(); });
                $property->setOption($option);
            }
            $tyreFilters[$key] = $property;
        }

        $countInListFilters++;
    }

    if (0 == $countInListFilters) {
        return;
    }

?>

<!-- фильтр "Бренды и параметры" -->
<div class="filter filter-options fltr" style="display: block">

    <form id="productCatalog-filter-form" class="fltrSet js-category-filter" action="<?= $baseUrl ?>" method="GET">

        <? if ($brandFilter1): ?>
            <!-- бренды -->
            <div class="fltrBtn_kit fltrBtn_kit--mark">
                <div class="fltrBtn_tggl fltrBtn_kit_l">
                    <i class="fltrBtn_tggl_corner icon-corner"></i>
                    <span class="dotted"><?= $brandFilter1->getName() ?></span>
                </div>

                <!-- список брендов -->
                <div class="fltrBtn_kit_r">

                    <?= $helper->render('category/filters/_brand', ['productFilter' => $productFilter, 'filter' => $brandFilter1]) ?>

                    <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>
                        <a href="#" class="fltrBtn_btn fltrBtn_btn-btn js-category-v2-filter-otherBrandsOpener"
                           <? if ($hasSelectedOtherBrands): ?>style="display: none;"<? endif ?>>
                            <span class="dotted">Ещё <?= count($brandFilter2->getOption()) ?></span>
                        </a>
                    <? endif ?>

                    <? if ($brandFilter2): ?>
                    <!-- больше брендов -->
                    <span class="js-category-v2-filter-otherBrands" style="display: <?= $hasSelectedOtherBrands ? 'inline' : 'none' ?>;">
                        <?= $helper->render('category/filters/_brand', ['productFilter' => $productFilter, 'filter' => $brandFilter2]) ?>
                    </span>
                    <!--/ больше брендов -->
                    <? endif ?>
                </div>
                <!--/ список брендов -->
            </div>
            <!--/ бренды -->
        <? endif ?>


        <div class="filter-price" style="padding-bottom: 10px;">
            <div class="fltrSet_tggl js-category-filter-otherParamsToggleButton">
                <span class="fltrSet_tggl_tx">Бренды и параметры</span>
            </div>

            <div class="fltrRange js-category-filter-rangeSlider js-category-v2-filter-element-price">
                <span class="fltrRange_lbl">от</span>
                <input class="fltrRange_it mFromRange js-category-filter-rangeSlider-from js-category-v2-filter-element-price-from" name="f-price-from" value="390" type="text" data-min="390">

                <div class="fltrRange_sldr js-category-filter-rangeSlider-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" data-config="{&quot;min&quot;:390,&quot;max&quot;:61990,&quot;step&quot;:10}" aria-disabled="false">
                    <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
                    <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
                    <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div></div>
                <span class="fltrRange_lbl">до</span>
                <input class="fltrRange_it mLast mToRange js-category-filter-rangeSlider-to js-category-v2-filter-element-price-to" name="f-price-to" value="61990" type="text" data-max="61990">

                <span class="fltrRange_val rubl">p</span>
            </div>
        </div>

        <div class="fltrSet_cnt js-category-v1-filter-otherParams">
            <!-- параметры фильтрации -->
            <div class="filter-content js-category-filter-otherParamsContent">
                <ul class="filter-params">
                    <li class="filter-params__item js-category-filter-param" data-ref="">
                        <span class="filter-params__text">WOW-товары</span>
                    </li>
                    <li class="filter-params__item js-category-filter-param mActive" data-ref="">
                        <span class="filter-params__text">Бренд</span>
                    </li>
                    <li class="filter-params__item js-category-filter-param" data-ref="">
                        <span class="filter-params__text">Наличие в магазинах</span>
                    </li>
                </ul>
                <!--/ параметры фильтрации -->

                <!-- ключи фильтрации -->
                <div class="filter-values">
                    <div class="filter-values__inner">
                        <!-- секция -->
                        <div class="filter-values__item js-category-filter-element" id="id-productCategory-filter-5-label" style="display: none;">
                            <div class="filter-values__cell">
                                <input class="custom-input filter-check jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-label-option-36" name="f-label-sale" value="36">
                                <label class="custom-label" for="id-productCategory-filter-label-option-36">
                                    Sale
                                </label>
                            </div>

                            <div class="filter-values__cell">
                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-label-option-36" name="f-label-sale" value="36">
                                <label class="custom-label" for="id-productCategory-filter-label-option-36">
                                    г. Сергиев Посад, ул. 1-я Рыбная, д. 19/22
                                </label>
                            </div>
                        </div>
                        <!-- /секция -->

                        <!-- секция -->
                        <div class="filter-values__item hf js-category-filter-element" id="id-productCategory-filter-5-brand" style="display: block;">
                            <div class="filter-values__cell">
                                <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-106" name="f-brand-disney" value="106" data-name="Disney">
                                <label class="custom-label" for="id-productCategory-filter-brand-option-106">
                                    Disney
                                </label>
                            </div>
                        </div>
                        <!--/ секция -->

                        <!-- секция -->
                        <div class="filter-values__item hf mLineItem js-category-filter-element" id="id-productCategory-filter-5-shop" style="display: none;">
                            <div class="filter-values__cell">
                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-8" name="shop" value="8">
                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-8">
                                    г. Сергиев Посад, ул. 1-я Рыбная, д. 19/22
                                </label>
                            </div>
                        </div>
                        <!--/ секция -->
                    </div>
                </div>
                <!--/ ключи фильтрации -->
            </div>
        </div>
    </form>
</div>
<!-- фильтр "Бренды и параметры" -->

<!-- фильтр "Ювелирный" -->
<div class="filter filter-components fltr" style="display: block">

        <!-- фильтр по компонентам -->
        <div class="fltrSet js-category-filter-toggle-container">
            <div class="fltrSet_tggl js-category-filter-toggle-button">
                <span class="fltrSet_tggl_tx">Металл</span>
            </div>

            <div class="fltrSet_cnt js-category-filter-toggle-content">
                <div class="fltrSet_inn">
                    <!-- секция -->
                    <div class="filter-values__cell">
                        <input class="custom-input jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop2194-option-29502" name="f-prop2194-beloe_zoloto_585" value="29502">
                        <label class="custom-label filter-img-box" for="id-productCategory-filter-prop2194-option-29502">
                            <span class="customLabel_wimg"></span>
                            <img class="customLabel_bimg" src="http://0.imgenter.ru/uploads/media/fb/ed/c8/8750e48fe26f4e02ea5776914561ea5228f5490e.png">

                            <span class="customLabel_btx">белое золото 585</span>
                        </label>
                    </div>
                    <!--/ секция -->

                    <!-- секция -->
                    <div class="filter-values__cell">
                        <input class="custom-input jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop2194-option-29506" name="f-prop2194-geltoe_zoloto_585" value="29506">
                        <label class="custom-label filter-img-box" for="id-productCategory-filter-prop2194-option-29506">
                            <span class="customLabel_wimg"></span>
                            <img class="customLabel_bimg" src="http://f.imgenter.ru/uploads/media/f1/1e/59/38c72c637f05867d9d5649e96179e8f60fa52342.png">

                            <span class="customLabel_btx">желтое золото 585</span>
                        </label>
                    </div>
                    <!--/ секция -->
                </div>
            </div>
        </div>
        <!--/ фильтр по компонентам -->

        <!-- фильтр по компонентам -->
        <div class="fltrSet js-category-filter-toggle-container">
            <div class="fltrSet_tggl js-category-filter-toggle-button">
                <span class="fltrSet_tggl_tx">Вставка</span>
            </div>

            <div class="fltrSet_cnt js-category-filter-toggle-content">
                <div class="fltrSet_inn">
                    <!-- секция -->
                    <div class="filter-values__cell">
                        <input class="custom-input jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-14381" name="f-prop7451-agat" value="14381">
                        <label class="custom-label filter-img-box" for="id-productCategory-filter-prop7451-option-14381">
                            <span class="customLabel_wimg"></span>
                            <img class="customLabel_bimg" src="http://2.imgenter.ru/uploads/media/be/4a/e9/b76078ce6a186de0b8e6bdd1c582f846e1831e6b.png">

                            <span class="customLabel_btx">агат</span>
                        </label>
                    </div>
                    <!--/ секция -->

                    <!-- секция -->
                    <div class="filter-values__cell">
                        <input class="custom-input jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-14382" name="f-prop7451-ametist" value="14382">
                        <label class="custom-label filter-img-box" for="id-productCategory-filter-prop7451-option-14382">
                            <span class="customLabel_wimg"></span>
                            <img class="customLabel_bimg" src="http://c.imgenter.ru/uploads/media/ae/b3/2b/2cb15804f1ac6129aace4e69892f0040de7e5a9c.png">

                            <span class="customLabel_btx">аметист</span>
                        </label>
                    </div>
                    <!--/ секция -->
                </div>
            </div>
        </div>
        <!-- фильтр по компонентам -->

        <!-- фильтр по цене -->
        <div class="fltrBtn_kit fltrBtn_kit-box">
            <div class="filter-price">
                <div class="fltrBtnBox js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                    <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                        <span class="fltrBtnBox_tggl_tx dotted">Цена</span>
                        <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                    </div>

                    <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                        <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                            <li class="lstdotted_i">
                                <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-to=12800">
                                    <span class="txmark1">до</span> 12&thinsp;800
                                </a>
                            </li>

                            <li class="lstdotted_i">
                                <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=12801&amp;f-price-to=25200">
                                    <span class="txmark1">от</span> 12&thinsp;801
                                    <span class="txmark1">до</span> 25&thinsp;200
                                </a>
                            </li>

                            <li class="lstdotted_i">
                                <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=50001">
                                    <span class="txmark1">от</span> 50&thinsp;001
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="fltrRange js-category-filter-rangeSlider js-category-v2-filter-element-price">
                    <span class="fltrRange_lbl">от</span>
                    <input class="fltrRange_it mFromRange js-category-filter-rangeSlider-from js-category-v2-filter-element-price-from" name="f-price-from" value="390" type="text" data-min="390">

                    <div class="fltrRange_sldr js-category-filter-rangeSlider-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" data-config="{&quot;min&quot;:390,&quot;max&quot;:61990,&quot;step&quot;:10}" aria-disabled="false">
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
                        <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div></div>
                    <span class="fltrRange_lbl">до</span>
                    <input class="fltrRange_it mLast mToRange js-category-filter-rangeSlider-to js-category-v2-filter-element-price-to" name="f-price-to" value="61990" type="text" data-max="61990">

                    <span class="fltrRange_val rubl">p</span>
                </div>
            </div>
        </div>
        <!--/ фильтр по цене -->

        <!-- тотже фильтр "Бренды и параметры" только "Ещё параметры" -->
        <div class="fltrSet">
            <div class="fltrSet_tggl js-category-filter-otherParamsToggleButton">
                <span class="fltrSet_tggl_tx">Ещё параметры</span>
            </div>

            <div class="fltrSet_cnt js-category-v1-filter-otherParams">
                <!-- параметры фильтрации -->
                <div class="filter-content js-category-filter-otherParamsContent">
                    <ul class="filter-params">
                        <li class="filter-params__item js-category-filter-param" data-ref="">
                            <span class="filter-params__text">WOW-товары</span>
                        </li>
                        <li class="filter-params__item js-category-filter-param mActive" data-ref="">
                            <span class="filter-params__text">Бренд</span>
                        </li>
                        <li class="filter-params__item js-category-filter-param" data-ref="">
                            <span class="filter-params__text">Наличие в магазинах</span>
                        </li>
                    </ul>
                    <!--/ параметры фильтрации -->

                    <!-- ключи фильтрации -->
                    <div class="filter-values">
                        <div class="filter-values__inner">
                            <!-- секция -->
                            <div class="filter-values__item js-category-filter-element" id="id-productCategory-filter-5-label" style="display: none;">
                                <div class="filter-values__cell">
                                    <input class="custom-input filter-check jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-label-option-36" name="f-label-sale" value="36">
                                    <label class="custom-label" for="id-productCategory-filter-label-option-36">
                                        Sale
                                    </label>
                                </div>

                                <div class="filter-values__cell">
                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-label-option-36" name="f-label-sale" value="36">
                                    <label class="custom-label" for="id-productCategory-filter-label-option-36">
                                        г. Сергиев Посад, ул. 1-я Рыбная, д. 19/22
                                    </label>
                                </div>
                            </div>
                            <!-- /секция -->

                            <!-- секция -->
                            <div class="filter-values__item hf js-category-filter-element" id="id-productCategory-filter-5-brand" style="display: block;">
                                <div class="filter-values__cell">
                                    <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-106" name="f-brand-disney" value="106" data-name="Disney">
                                    <label class="custom-label" for="id-productCategory-filter-brand-option-106">
                                        Disney
                                    </label>
                                </div>
                            </div>
                            <!--/ секция -->

                            <!-- секция -->
                            <div class="filter-values__item hf mLineItem js-category-filter-element" id="id-productCategory-filter-5-shop" style="display: none;">
                                <div class="filter-values__cell">
                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-8" name="shop" value="8">
                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-8">
                                        г. Сергиев Посад, ул. 1-я Рыбная, д. 19/22
                                    </label>
                                </div>
                            </div>
                            <!--/ секция -->
                        </div>
                    </div>
                    <!--/ ключи фильтрации -->
                </div>
            </div>
        </div>
        <!--/ тотже фильтр "Бренды и параметры" только "Ещё параметры" -->
</div>
<!-- фильтр "Ювелирный" -->

<!-- фильтр по брендам -->
<div class="filter filter-brands fltrBtn" style="display: block">

        <div class="fltrBtn_kit fltrBtn_kit-box">

            <div class="filter-price">
                <div class="fltrBtnBox js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                    <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                        <span class="fltrBtnBox_tggl_tx dotted">Цена</span>
                        <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                    </div>

                    <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                        <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                            <li class="lstdotted_i">
                                <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-to=12800">
                                    <span class="txmark1">до</span> 12&thinsp;800
                                </a>
                            </li>

                            <li class="lstdotted_i">
                                <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=12801&amp;f-price-to=25200">
                                    <span class="txmark1">от</span> 12&thinsp;801
                                    <span class="txmark1">до</span> 25&thinsp;200
                                </a>
                            </li>

                            <li class="lstdotted_i">
                                <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=50001">
                                    <span class="txmark1">от</span> 50&thinsp;001
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="fltrRange js-category-filter-rangeSlider js-category-v2-filter-element-price">
                    <span class="fltrRange_lbl">от</span>
                    <input class="fltrRange_it mFromRange js-category-filter-rangeSlider-from js-category-v2-filter-element-price-from" name="f-price-from" value="390" type="text" data-min="390">

                    <div class="fltrRange_sldr js-category-filter-rangeSlider-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" data-config="{&quot;min&quot;:390,&quot;max&quot;:61990,&quot;step&quot;:10}" aria-disabled="false">
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
                        <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div></div>
                    <span class="fltrRange_lbl">до</span>
                    <input class="fltrRange_it mLast mToRange js-category-filter-rangeSlider-to js-category-v2-filter-element-price-to" name="f-price-to" value="61990" type="text" data-max="61990">

                    <span class="fltrRange_val rubl">p</span>
                </div>
            </div>
        </div>

        <div class="fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
            <!--
                секция фильтрации
                чтобы открыть добавляем класс opn, для поселекченного состояния класс - actv
            -->
            <div class="fltrBtnBox js-category-v2-filter-dropBox">
                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                    <span class="fltrBtnBox_tggl_tx dotted">В магазине</span>
                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                </div>

                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                    <div class="fltrBtnBox_dd_inn">
                        <div class="fltrBtn_param">
                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-2" name="shop" value="2">
                                <label class="custom-label" for="id-productCategory-filter-shop-option-2">
                                    ул. Орджоникидзе, д. 11, стр. 10
                                </label>
                            </div>

                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-13" name="shop" value="13">
                                <label class="custom-label" for="id-productCategory-filter-shop-option-13">
                                    Волгоградский пр-т, д. 119а.
                                </label>
                            </div>

                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-68" name="shop" value="68">
                                <label class="custom-label" for="id-productCategory-filter-shop-option-68">
                                    Свободный пр-кт, д. 33
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ секция фильтрации -->

            <!--
                секция фильтрации
                чтобы открыть добавляем класс opn, для поселекченного состояния класс - actv
            -->
            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                    <span class="fltrBtnBox_tggl_tx dotted">Платформа</span>
                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                </div>

                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                    <div class="fltrBtnBox_dd_inn">
                        <div class="fltrBtn_param">
                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-5337" name="f-prop3826-android" value="5337">
                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-5337">
                                    Android
                                </label>
                            </div>

                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29929" name="f-prop3826-android_4_0_ics" value="29929">
                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-29929">
                                    Android 4.0 ICS
                                </label>
                            </div>

                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29928" name="f-prop3826-android_4_1_jelly_bean" value="29928">
                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-29928">
                                    Android 4.1 Jelly Bean
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ секция фильтрации -->

            <!--
                секция фильтрации
                чтобы открыть добавляем класс opn, для поселекченного состояния класс - actv
            -->
            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                    <span class="fltrBtnBox_tggl_tx dotted">Дисплей</span>
                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                </div>

                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                    <div class="fltrBtnBox_dd_inn">
                        <div class="fltrBtn_param">
                            <div class="fltrBtn_param_n">Диагональ экрана</div>

                            <div class="fltrBtn_ln js-category-v2-filter-element-number">
                                <span class="fltrBtn_param_lbl txmark1">от</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-from" name="" value="" placeholder="1.4" type="text">
                                &ensp;<span class="fltrBtn_param_lbl txmark1">до</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-to" name="" value="" placeholder="6" type="text">
                                <span class="fltrBtn_param_lbl txmark1">"</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ секция фильтрации -->
        </div>

        <!-- поселекченные фильтры -->
        <div class="fltrBtn_kit fltrBtn_kit-nborder">
            <div class="js-category-filter-selected">
                <ul class="fltr_slctd">
                    <li class="fltr_slctd_i fltr_slctd_i-n">В магазине:</li>

                    <li class="fltr_slctd_i">
                        <span>ул. Орджоникидзе, д. 11, стр. 10</span>
                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                    </li>
                </ul>

                <ul class="fltr_slctd">
                    <li class="fltr_slctd_i fltr_slctd_i-n">Память:</li>

                    <li class="fltr_slctd_i">
                        Встроенная память

                        <span>от 5 ГБ</span>
                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                    </li>
                </ul>

                <ul class="fltr_slctd">
                    <li class="fltr_slctd_i fltr_slctd_i-n">Платформа:</li>

                    <li class="fltr_slctd_i">
                        <span>Android 4.1 Jelly Bean</span>
                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                    </li>
                </ul>

                <a class="fltr_clsr jsHistoryLink" href="">
                    <span class="btn-closer3"></span>
                    <span class="dotted">Очистить все</span>
                </a>
            </div>
        </div>
        <!--/ поселекченные фильтры -->
</div>
<!--/ фильтр по брендам -->