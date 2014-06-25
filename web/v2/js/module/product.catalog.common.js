define(
    [
        'jquery',
        'module/config', 'jquery.ui', 'jquery.photoswipe'
    ],
    function ($, config) {
        var $slider = $('.js-rangeSlider-container');

        $slider.each(function(i, el) {
            var $el = $(el),
                $slider = $el.find('.js-rangeSlider'),
                $fromInput = $el.find('.js-rangeSlider-from'),
                $toInput = $el.find('.js-rangeSlider-to'),
                dataValue = $el.data('value'),

                updateInput = function(e) {
                    var value = '0' + $(this).val();

                    value = parseFloat(value);
                    value =
                        (value > dataValue.max) ? dataValue.max :
                        ( value < dataValue.min ) ? dataValue.min :
                        value;

                    $(this).val(value);

                    $slider.slider({
                        values: [
                            $fromInput.val(),
                            $toInput.val()
                        ]
                    });
                }
            ;

            if (!dataValue || !$slider.length) {
                console.warn('slider', $el, dataValue);
                return true;
            } else {
                console.info('slider', $el, dataValue);
            }

            $slider.slider({
                range: true,
                step: dataValue.step,
                min: dataValue.min,
                max: dataValue.max,
                values: [
                    $fromInput.val(),
                    $toInput.val()
                ],

                slide: function(e, ui) {
                    $fromInput.val(ui.values[0]);
                    $toInput.val(ui.values[1]);
                },

                change: function(e, ui) {
                    if (e.originalEvent) {
                        console.info('js-rangeSlider', $slider, ui, e);

                        if (ui.value == ui.values[0]) {
                            $fromInput.trigger('change');
                        }
                        if (ui.value == ui.values[1]) {
                            $toInput.trigger('change');
                        }
                    }
                }
            });

            $fromInput.on('change', updateInput);
            $toInput.on('change', updateInput);
        });

        var paramsBtn = $('.js-action-params'),
            params = $('.js-params'),

            w = $(window),
            paramsBtnFixed = $('.js-params-btns-fixed'),
            scrollTarget = $('.js-params-btns'),
            scrollTargetOffset,

            paramsTitle = $('.js-params-title'),
            paramsCont = $('.js-params-cont'),

            sortBtn = $('.sortingAbb .sortingSelect');
        // end of vars
        
        var 
            /**
             * Показать/скрыть доп понельку кнопок для фильтрации
             */
            paramsBtnShow = function paramsBtnShow() {
                var
                    nowScroll = w.scrollTop(),
                    nowScrollHeight = w.height();
                // end of vars   
                
                scrollTargetOffset = scrollTarget.offset().top + scrollTarget.height();

                if ( nowScroll <= Math.abs(nowScrollHeight - scrollTargetOffset) && scrollTargetOffset >= nowScrollHeight ) {
                    paramsBtnFixed.addClass('paramsBtnFixed-show');
                } else {
                    paramsBtnFixed.removeClass('paramsBtnFixed-show');
                }
            },

            /**
             * Показать/скрыть параметры фильтрации
             */
            paramsAction = function paramsAction( event ) { 
                var $self = $(this);

                params.toggleClass('params-open');
                $self.toggleClass('catalogHead-open');

                paramsTitle.removeClass('params_title-open');
                paramsCont.removeClass('params_cont-open');

                if ( params.hasClass('params-open') ) {
                    scrollTargetOffset = scrollTarget.offset().top + scrollTarget.height();
                    paramsBtnShow();
                    w.on('scroll', paramsBtnShow);
                } else {
                    paramsBtnFixed.removeClass('paramsBtnFixed-show');
                    w.on('scroll', function() {paramsBtnFixed.removeClass('paramsBtnFixed-show');});
                } 

                console.warn('params open')
            },

            /**
             * Показать/скрыть подробности параметров фильтрации
             */
            paramsItem = function paramsItem() {
                var $self = $(this);

                $self.toggleClass('params_title-open').next('.params_cont').toggleClass('params_cont-open');

                paramsBtnShow(); /** пересчет scrollTargetOffset при открытии параметра **/
            },

            showSortingList = function showSortingList() {
                sortList = $('.sortingAbb .sortingList');

                sortList.slideToggle();
            };
        // end of functions
        
        paramsBtn.on('click', paramsAction);
        paramsTitle.on('click', paramsItem);

        $('#id-productSorting').on('click', '.js-productSorting-select', showSortingList);
    }
);