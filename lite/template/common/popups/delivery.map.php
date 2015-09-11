<script id="js-points-popup-template" type="text/template" class="hidden">
    <div class="popup">
        <div class="popup__close js-popup-close"></div>
        <div class="popup__title popup__title_mb10 popup__title_">
            {{#isShowroom}}
                На витрине в
                {{#points.1}}
                    магазинах
                {{/points.1}}

                {{^points.1}}
                    магазине
                {{/points.1}}
            {{/isShowroom}}

            {{^isShowroom}}
                <?= (isset($title)) ? $title : 'Выберите точку самовывоза' ?>
            {{/isShowroom}}
        </div>

        <!-- Новая верстка -->
        <div class="delivery-points">
            <div class="delivery-points__left">
                {{^isShowroom}}
                    <div class="point-search">
                        <i class="point-search__icon i-controls i-controls--search"></i>
                        <input class="point-search__it it js-pointpopup-search" type="text" placeholder="Искать по улице, метро">
                        <div class="point-search" style="display: none;">×</div>

                        <div class="pick-point-suggest js-pointpopup-autocomplete" style="display: none">
                            <ul class="pick-point-suggest__list js-pointpopup-autocomplete-wrapper"></ul>
                        </div>
                    </div>

                    <div class="drop-filter-kit drop-filter-kit-box">

                        <!-- Точки самовывоза - для поселекченного фильтра добавляем класс active-->
                        <div class="drop-filter-box js-point-filter">
                            <div class="drop-filter-box__tggl js-point-filter-opener">
                                <span class="drop-filter-box__tggl-tx">Все точки</span>
                            </div>

                            <div class="drop-filter-box__dd">
                                <div class="drop-filter-box__dd-inn">
                                    {{#uniqueTokens}}
                                    <div class="drop-filter-box__dd-line">
                                        <input class="custom-input js-point-filter-param custom-input_check-fill" type="checkbox" id="point_{{key}}_{{value}}" name="{{key}}" value="{{value}}">
                                        <label class="custom-label" for="point_{{key}}_{{value}}">
                                            {{displayValue}}

                                            {{#help}}
                                            <!-- попап-подсказка с описание пункта самовывоза -->
                                            <div class="delivery-points-info delivery-points-info_inline">
                                                <a class="delivery-points-info__icon"></a>
                                                <div class="delivery-points-info__popup delivery-points-info__popup_top info-popup">
                                                    <a class="delivery-points-info__link" href="{{help.url}}" title="{{help.name}}" target="_blank">{{help.name}}</a>
                                                </div>
                                            </div>
                                            <!--/ попап-подсказка с описание пункта самовывоза -->
                                            {{/help}}
                                        </label>
                                    </div>
                                    {{/uniqueTokens}}

                                </div>
                            </div>
                        </div>
                        <!--/ Точки самовывоза -->


                        <!-- Cтоимость -->
                        <div class="drop-filter-box js-point-filter">
                            <div class="drop-filter-box__tggl js-point-filter-opener">
                                <span class="drop-filter-box__tggl-tx">Стоимость</span>
                            </div>

                            <div class="drop-filter-box__dd">
                                <div class="drop-filter-box__dd-inn">
                                    {{#uniqueCosts}}
                                    <div class="drop-filter-box__dd-line">
                                        <input class="custom-input js-point-filter-param custom-input_check" type="checkbox" id="cost_{{key}}_{{value}}" name="{{key}}" value="{{value}}">
                                        <label class="custom-label" for="cost_{{key}}_{{value}}">
                                            <span class="customLabel_btx">{{displayValue}}</span>
                                        </label>
                                    </div>
                                     {{/uniqueCosts}}
                                </div>
                            </div>
                        </div>
                        <!-- Cтоимость -->

                        <!-- Дата самовывоза -->
                        <div class="drop-filter-box js-point-filter">
                            <div class="drop-filter-box__tggl js-point-filter-opener">
                                <span class="drop-filter-box__tggl-tx">Дата</span>
                            </div>

                            <div class="drop-filter-box__dd">
                                <div class="drop-filter-box__dd-inn">
                                    {{#uniqueDays}}
                                    <div class="drop-filter-box__dd-line">
                                        <input class="custom-input js-point-filter-param custom-input_check" type="checkbox" id="date_{{key}}_{{value}}" name="{{key}}" value="{{value}}">
                                        <label class="custom-label" for="date_{{key}}_{{value}}">
                                            <span class="customLabel_btx">{{displayValue}}</span>
                                        </label>
                                    </div>
                                    {{/uniqueDays}}
                                </div>
                            </div>
                        </div>
                        <!--/ Дата самовывоза -->
                    </div>

                    <span class="delivery-points-nomatch" style="display: none;">Поиск не дал результатов</span>
                {{/isShowroom}}

                <div class="delivery-points-lwrap">
                    <div class="delivery-points-lwrap__inn {{#isShowroom}}delivery-points-lwrap__inn_455{{/isShowroom}}">
                        <div class="delivery-points-list table js-pointpopup-points-wrapper"></div>
                    </div>
                </div>
            </div>

            <div class="delivery-points__right js-pointpopup-map-container"></div>
        </div>

        {{#isShowroom}}
            <div class="delivery-points-warning">Чтобы купить товар с витрины, нужно приехать в магазин и обратиться к продавцу</div>
        {{/isShowroom}}
    </div>
</script>

<script id="js-point-template" type="text/template" class="hidden">
{{#point}}
    {{#shown}}
        <!-- точка доставки в которой товар есть на витрине - добавляем класс no-hidden -->
        <div class="js-pointpopup-pick-point delivery-points-list__row table-row no-hidden {{#productInShowroom}}no-hidden{{/productInShowroom}}"
            data-id="{{id}}"
            data-token="{{token}}"
        >
            <div class="delivery-points-list__logo table-cell">
                <img src="{{icon}}" class="delivery-points-list__img">

                {{#help}}
                    <!-- попап-подсказка с описание пункта самовывоза -->
                    <div class="delivery-points-info delivery-points-info_absolute">
                        <a class="delivery-points-info__icon"></a>
                        <div class="delivery-points-info__popup delivery-points-info__popup_left info-popup">
                            <a class="delivery-points-info__link js-pointpopup-pick-point-help" href="{{help.url}}" title="{{help.name}}" target="_blank">{{help.name}}</a>
                        </div>
                    </div>
                    <!--/ попап-подсказка с описание пункта самовывоза -->
                {{/help}}

                <span class="delivery-points-list__name">{{listName}}</span>
            </div>

            <div class="delivery-points-list__address table-cell">
                <div class="delivery-points-list__address-name">{{address}}</div>
                <div class="delivery-points-list__address-time">{{regtime}}</div>
            </div>

            {{^isShowroom}}
                <div class="delivery-points-list__info table-cell">
                    <div class="delivery-points-list__info-hidden">
                        {{^productInShowroom}}
                            <div class="delivery-points-list__info-date">{{humanNearestDay}}</div>
                            <div class="delivery-points-list__info-price"><span>{{humanCost}}</span>{{#showCurrency}}&thinsp;<span class="rubl">C</span>{{/showCurrency}}</div>
                        {{/productInShowroom}}
                    </div>

                    {{#showBuyButton}}
                    <div class="delivery-points-list__info-btn">
                        <a href="" class="btn-primary btn-primary_middle">Купить</a>
                    </div>
                    {{/showBuyButton}}

                    {{#productInShowroom}}
                    <div class="delivery-points-list__info-hidden">
                        <span class="delivery-points-list__info-price">На витрине</span>
                        <!-- попап-подсказка с описание пункта самовывоза -->
                        <div class="delivery-points-info delivery-points-info_inline">
                            <i class="i-product i-product--info-normal i-info__icon"></i>

                            <div class="delivery-points-info__popup delivery-points-info__popup_right info-popup">
                                Чтобы купить товар с витрины,<br>нужно приехать в магазин и обратиться к продавцу.
                            </div>
                        </div>
                    </div>
                    <!--/ попап-подсказка с описание пункта самовывоза -->

                    {{/productInShowroom}}
                </div>
            {{/isShowroom}}
        </div>
    {{/shown}}
{{/point}}
</script>
