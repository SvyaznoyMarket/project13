<script id="js-points-popup-template" type="text/template" class="hidden">
    <div class="delivery-points popup">
        <div class="popup__close js-popup-close">×</div>
        <div class="popup__title">Выберите точку самовывоза</div>

        <!-- Новая верстка -->
        <div class="delivery-points__left">
            <div class="point-search">
                <i class="point-search__icon i-controls i-controls--search"></i>
                <input class="point-search__it it" type="text" placeholder="Искать по улице, метро">
                <div class="point-search" style="display: none;">×</div>

                <div class="pick-point-suggest" style="display: none">
                    <ul class="pick-point-suggest__list"></ul>
                </div>
            </div>

            <div class="drop-filter-kit drop-filter-kit-box">
                <!-- Точки самовывоза - для поселекченного фильтра добавляем класс active-->
                <div class="drop-filter-box open">
                    <div class="drop-filter-box__tggl">
                        <span class="drop-filter-box__tggl-tx">Все точки</span>
                    </div>

                    <div class="drop-filter-box__dd">
                        <div class="drop-filter-box__dd-inn">
                            <div class="drop-filter-box__dd-line">
                                <input class="custom-input custom-input_check-fill" type="checkbox" id="shops" name="" value="">
                                <label class="custom-label" for="shops">Магазины Enter</label>
                                <!-- попап-подсказка с описание пункта самовывоза -->
                                <div class="delivery-points-info delivery-points-info_inline">
                                    <a class="delivery-points-info__icon"></a>
                                    <div class="delivery-points-info__popup delivery-points-info__popup_top info-popup">
                                        <a class="delivery-points-info__link" href="" title="Как пользоваться постаматом">Как пользоваться постаматом</a>
                                    </div>
                                </div>
                                <!--/ попап-подсказка с описание пункта самовывоза -->
                            </div>

                            <div class="drop-filter-box__dd-line">
                                <input class="custom-input custom-input_check-fill" type="checkbox" id="pick" name="" value="">
                                <label class="custom-label" for="pick">Постаматы PickPoint</label>
                                <!-- попап-подсказка с описание пункта самовывоза -->
                                <div class="delivery-points-info delivery-points-info_inline">
                                    <a class="delivery-points-info__icon"></a>
                                    <div class="delivery-points-info__popup delivery-points-info__popup_top info-popup">
                                        <a class="delivery-points-info__link" href="" title="Как пользоваться постаматом">Как пользоваться постаматом</a>
                                    </div>
                                </div>
                                <!--/ попап-подсказка с описание пункта самовывоза -->
                            </div>

                            <div class="drop-filter-box__dd-line">
                                <input class="custom-input custom-input_check-fill" type="checkbox" id="shops1" name="" value="">
                                <label class="custom-label" for="shops1">Магазины Enter</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Точки самовывоза -->

                <!-- Cтоимость -->
                <div class="drop-filter-box">
                    <div class="drop-filter-box__tggl">
                        <span class="drop-filter-box__tggl-tx">Стоимость</span>
                    </div>

                    <div class="drop-filter-box__dd">
                        <div class="drop-filter-box__dd-inn">
                            <div class="fltrBtn_param">
                                <div class="fltrBtn_ln ">
                                    <input class="custom-input custom-input_check" type="checkbox" id="" name="" value="0" data-bind="checked: $root.choosenCosts">
                                    <label class="custom-label" for="">
                                        <span class="customLabel_btx">Бесплатно</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cтоимость -->

                <!-- Дата самовывоза -->
                <div class="drop-filter-box">
                    <div class="drop-filter-box__tggl">
                        <span class="drop-filter-box__tggl-tx">Дата</span>
                    </div>

                    <div class="drop-filter-box__dd">
                        <div class="drop-filter-box__dd-inn">
                            <div class="fltrBtn_param">
                                <div class="fltrBtn_ln ">
                                    <input class="custom-input custom-input_check" type="checkbox" id="" name="" value="2015-08-11">
                                    <label class="custom-label" for="">
                                        <span class="customLabel_btx">11.08.2015</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Дата самовывоза -->
            </div>

            <span class="delivery-points-nomatch" style="display: none;">Поиск не дал результатов</span>

            <div class="delivery-points-lwrap">
                <div class="delivery-points-lwrap__inn">
                    <div class="delivery-points-list table js-pointpopup-points-wrapper"></div>
                </div>
            </div>
        </div>

        <div class="delivery-points__right js-pointpopup-map-container"></div>
    </div>
</script>

<script id="js-point-template" type="text/template" class="hidden">
{{#point}}
    {{#shown}}
        <!-- точка доставки в которой товар есть на витрине - добавляем класс no-hidden -->
        <div class="delivery-points-list__row table-row {{#productInShowroom}}no-hidden{{/productInShowroom}}">
            <div class="delivery-points-list__logo table-cell">
                <img src="{{icon}}" class="delivery-points-list__img">

                <!-- попап-подсказка с описание пункта самовывоза -->
                <div class="delivery-points-info delivery-points-info_absolute">
                    <a class="delivery-points-info__icon"></a>
                    <div class="delivery-points-info__popup delivery-points-info__popup_left info-popup">
                        <a class="delivery-points-info__link" href="" title="Как пользоваться постаматом">Как пользоваться постаматом</a>
                    </div>
                </div>
                <!--/ попап-подсказка с описание пункта самовывоза -->

                <span class="delivery-points-list__name">{{listName}}</span>
            </div>

            <div class="delivery-points-list__address table-cell">
                <div class="delivery-points-list__address-name">{{address}}</div>
                <div class="delivery-points-list__address-time">{{regtime}}</div>
            </div>

            <div class="delivery-points-list__info table-cell">
                <div class="delivery-points-list__info-hidden">
                    <div class="delivery-points-list__info-date">{{humanNearestDay}}</div>
                    <div class="delivery-points-list__info-price"><span>{{cost}}</span></div>
                </div>

                {{#showBuyButton}}
                <div class="delivery-points-list__info-btn">
                    <a href="" class="btn-primary btn-primary_middle">Купить</a>
                </div>
                {{/showBuyButton}}

                {{#productInShowroom}}
                <div class="delivery-points-list__info table-cell">
                    <span class="delivery-points-list__info-price">На витрине</span>
                    <!-- попап-подсказка с описание пункта самовывоза -->
                    <div class="delivery-points-info delivery-points-info_inline">
                        <i class="i-product i-product--info-normal i-info__icon"></i>

                        <div class="delivery-points-info__popup delivery-points-info__popup_right info-popup">
                            Чтобы купить товар с витрины,<br>нужно приехать в магазин и обратиться к продавцу.
                        </div>
                    </div>
                    <!--/ попап-подсказка с описание пункта самовывоза -->
                </div>
                {{/productInShowroom}}
            </div>
        </div>
    {{/shown}}
{{/point}}
</script>